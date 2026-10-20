<?php
require_once __DIR__ . '/_boot.php';
// first run: create the admin file if missing
$a = admin_data();
if (!$a) { store_write('admin', ['username' => 'admin', 'hash' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), 'must_change' => 1]); $a = admin_data(); }
if (!empty($_SESSION['uid'])) redirect(aurl());

$err = '';
$att = store_read('attempts', []);
$ip = client_ip();
$att = array_filter($att, fn($x) => ($x['t'] ?? 0) > time() - 900);
$mine = $att[$ip]['n'] ?? 0;
if (is_post()) {
    csrf_check();
    if ($mine >= 6) $err = 'Too many failed attempts. Please wait 15 minutes and try again.';
    else {
        $u = trim($_POST['username'] ?? ''); $p = $_POST['password'] ?? '';
        if (hash_equals($a['username'], $u) && password_verify($p, $a['hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = $a['username'];
            unset($att[$ip]); store_write('attempts', $att);
            redirect(aurl());
        }
        $att[$ip] = ['n' => $mine + 1, 't' => time()]; store_write('attempts', $att);
        $err = 'Incorrect username or password.';
        usleep(400000);
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Login · Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>"><link rel="stylesheet" href="<?= asset('assets/vendor/icons/bootstrap-icons.min.css') ?>">
<link rel="stylesheet" href="<?= aurl('assets/admin.css') ?>?v=1"><style>:root{--brand:<?= e(hex_ok2(setting('color_primary'), '#0287BF')) ?>}</style></head>
<body class="login-body"><div class="login-card">
  <a class="login-brand" href="<?= url('') ?>"><img src="<?= e(asset(setting('logo') ?: 'assets/img/logo.svg')) ?>" height="34" alt=""><span><?= e(setting('site_name')) ?></span></a>
  <h1>Dashboard login</h1><p class="text-muted">Sign in to manage your website.</p>
  <?php if ($err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endif; ?>
  <form method="post" autocomplete="on"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Username</label><input class="form-control form-control-lg" name="username" autofocus required autocomplete="username"></div>
    <div class="mb-4"><label class="form-label">Password</label><input class="form-control form-control-lg" type="password" name="password" required autocomplete="current-password"></div>
    <button class="btn btn-brand btn-lg w-100">Sign in</button>
  </form>
  <a class="back" href="<?= url('') ?>">← Back to website</a>
</div></body></html>
