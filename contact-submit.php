<?php
require_once __DIR__ . '/includes/mailer.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Invalid request'], 405);

if (!empty($_POST['website'])) json_out(['ok' => true]);                 // honeypot -> pretend success
$ts = (int)($_POST['ts'] ?? 0);
if ($ts && time() - $ts < 3) json_out(['ok' => false, 'error' => 'Please take a moment to fill in the form.'], 429);
$_SESSION['cs'] = array_filter($_SESSION['cs'] ?? [], fn($t) => $t > time() - 600);
if (count($_SESSION['cs']) >= 5) json_out(['ok' => false, 'error' => 'Too many messages. Please try again later.'], 429);

$name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $phone = trim($_POST['phone'] ?? '');
$topic = trim($_POST['topic'] ?? ''); $message = trim($_POST['message'] ?? '');
if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) json_out(['ok' => false, 'error' => 'Please fill in your name, a valid email and a message.'], 422);
if (preg_match('/[\r\n]/', $name . $email)) json_out(['ok' => false, 'error' => 'Invalid input.'], 422);
$cut = fn($s, $n) => function_exists('mb_substr') ? mb_substr($s, 0, $n) : substr($s, 0, $n);
$name = $cut($name, 120); $phone = $cut($phone, 40); $topic = $cut($topic, 80); $message = $cut($message, 4000);

$leads = store_read('leads', []);
$id = bin2hex(random_bytes(5));
$lead = ['id' => $id, 'name' => $name, 'email' => $email, 'phone' => $phone, 'topic' => $topic, 'message' => $message, 'ip' => client_ip(), 'date' => date('c'), 'read' => 0, 'mail' => ''];

$body = "New message from the website\n\nName: $name\nEmail: $email\nPhone: " . ($phone ?: '-') . "\nTopic: " . ($topic ?: '-') . "\n\nMessage:\n$message\n\n— Sent " . date('Y-m-d H:i') . " UTC from " . client_ip();
[$ok, $info] = mail_send(setting('notify_email'), '[' . setting('site_name') . '] ' . ($topic ?: 'New contact message') . ' — ' . $name, $body, ['reply_to' => $email]);
$lead['mail'] = $ok ? 'sent' : 'failed: ' . $info;

if ($ok && setting('auto_reply_on')) {
    $ar = str_replace(['{name}'], [$name], setting('auto_reply_body'));
    mail_send($email, setting('auto_reply_subject') ?: 'We received your message', $ar);
}
array_unshift($leads, $lead);
store_write('leads', array_slice($leads, 0, 2000));
$_SESSION['cs'][] = time();
// The message is saved in the dashboard even if email delivery failed.
json_out(['ok' => true]);
