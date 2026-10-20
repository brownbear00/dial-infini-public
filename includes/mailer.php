<?php
/** Minimal dependency-free SMTP client (SSL / STARTTLS / plain, AUTH LOGIN/PLAIN). */
require_once __DIR__ . '/bootstrap.php';

function smtp_cfg() {
    $c = store_read('smtp');
    foreach (smtp_fields() as $k => $f) if (!isset($c[$k])) $c[$k] = $f['default'] ?? '';
    return $c;
}

function mail_send($to, $subject, $body, $opts = []) {
    try { return mail_send_inner($to, $subject, $body, $opts); } catch (Throwable $e) { return [false, 'Mail error: ' . $e->getMessage()]; }
}
function mail_send_inner($to, $subject, $body, $opts = []) {
    $cfg = smtp_cfg();
    $fromEmail = $opts['from'] ?? ($cfg['smtp_from'] ?: ($cfg['smtp_user'] ?: 'no-reply@' . preg_replace('/^www\./', '', parse_url(site_origin(), PHP_URL_HOST) ?: 'localhost')));
    $fromName = $opts['from_name'] ?? ($cfg['smtp_from_name'] ?: setting('site_name'));
    $replyTo = $opts['reply_to'] ?? '';
    $html = !empty($opts['html']);
    $recipients = array_values(array_filter(array_map('trim', is_array($to) ? $to : explode(',', $to)), fn($x) => filter_var($x, FILTER_VALIDATE_EMAIL)));
    if (!$recipients) return [false, 'No valid recipient address'];

    $enc = fn($s) => preg_match('/[^\x20-\x7E]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s;
    $domain = substr(strrchr($fromEmail, '@'), 1) ?: 'localhost';
    $headers = [
        'Date: ' . date('r'),
        'From: ' . $enc($fromName) . ' <' . $fromEmail . '>',
        'To: ' . implode(', ', $recipients),
        'Subject: ' . $enc($subject),
        'Message-ID: <' . bin2hex(random_bytes(8)) . '@' . $domain . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/' . ($html ? 'html' : 'plain') . '; charset=UTF-8',
        'Content-Transfer-Encoding: base64',
    ];
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) $headers[] = 'Reply-To: ' . $replyTo;
    $payload = chunk_split(base64_encode($body), 76, "\r\n");

    if (empty($cfg['smtp_on']) || !$cfg['smtp_host']) {
        $ok = @mail(implode(',', $recipients), $enc($subject), $payload, implode("\r\n", array_filter($headers, fn($h) => !preg_match('/^(To|Subject|Date):/', $h))), '-f' . $fromEmail);
        return [$ok, $ok ? 'Sent via PHP mail()' : 'PHP mail() failed (server may not allow it – enable SMTP)'];
    }

    $host = $cfg['smtp_host']; $port = (int)($cfg['smtp_port'] ?: 465); $sec = $cfg['smtp_secure'];
    $remote = ($sec === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => false, 'SNI_enabled' => true]]);
    $log = [];
    $fp = @stream_socket_client($remote, $en, $es, 15, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) return [false, "Could not connect to $host:$port ($es)"];
    stream_set_timeout($fp, 15);
    $read = function () use ($fp, &$log) { $d = ''; while (($l = fgets($fp, 515)) !== false) { $d .= $l; if (strlen($l) < 4 || $l[3] === ' ') break; } $log[] = 'S: ' . trim($d); return $d; };
    $cmd = function ($c, $expect) use ($fp, $read, &$log) {
        fwrite($fp, $c . "\r\n");
        $log[] = 'C: ' . $c;
        $r = $read();
        return [in_array(substr($r, 0, 3), (array)$expect, true), $r];
    };
    $fail = function ($m) use ($fp, &$log) { @fwrite($fp, "QUIT\r\n"); @fclose($fp); return [false, $m]; };

    $r = $read(); if (substr($r, 0, 3) !== '220') return $fail("Bad greeting: " . trim($r));
    $ehlo = gethostname() ?: 'localhost';
    [$ok, $r] = $cmd("EHLO $ehlo", '250'); if (!$ok) return $fail("EHLO rejected: " . trim($r));
    if ($sec === 'tls') {
        [$ok, $r] = $cmd('STARTTLS', '220'); if (!$ok) return $fail("STARTTLS rejected: " . trim($r));
        if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) return $fail('TLS handshake failed');
        [$ok, $r] = $cmd("EHLO $ehlo", '250'); if (!$ok) return $fail("EHLO after TLS rejected");
    }
    if ($cfg['smtp_user'] !== '') {
        [$ok, $r] = $cmd('AUTH LOGIN', '334'); if (!$ok) return $fail("AUTH not accepted: " . trim($r));
        [$ok, $r] = $cmd(base64_encode($cfg['smtp_user']), '334'); if (!$ok) return $fail("Username rejected: " . trim($r));
        [$ok, $r] = $cmd(base64_encode($cfg['smtp_pass']), '235'); if (!$ok) return $fail("Login failed – check username/password. Server said: " . trim($r));
    }
    [$ok, $r] = $cmd("MAIL FROM:<$fromEmail>", '250'); if (!$ok) return $fail("Sender rejected: " . trim($r));
    foreach ($recipients as $rc) { [$ok, $r] = $cmd("RCPT TO:<$rc>", ['250', '251']); if (!$ok) return $fail("Recipient rejected ($rc): " . trim($r)); }
    [$ok, $r] = $cmd('DATA', '354'); if (!$ok) return $fail("DATA rejected: " . trim($r));
    $msg = implode("\r\n", $headers) . "\r\n\r\n" . $payload;
    fwrite($fp, $msg . "\r\n.\r\n");
    $r = $read(); if (substr($r, 0, 3) !== '250') return $fail("Message rejected: " . trim($r));
    @fwrite($fp, "QUIT\r\n"); @fclose($fp);
    return [true, 'Sent via SMTP'];
}
