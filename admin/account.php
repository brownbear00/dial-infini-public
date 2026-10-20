<?php
require_once __DIR__ . '/_boot.php';
require_login(true);
$a = admin_data();
if (is_post()) {
    csrf_check();
    $cur = $_POST['current'] ?? ''; $new = $_POST['new'] ?? ''; $user = trim($_POST['username'] ?? '');
    if (!password_verify($cur, $a['hash'])) flash('Current password is incorrect.', 'danger');
    elseif (!preg_match('/^[A-Za-z0-9._-]{3,40}$/', $user)) flash('Username must be 3–40 characters (letters, numbers, . _ -).', 'danger');
    elseif ($new !== '' && (strlen($new) < 10 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new))) flash('New password must be at least 10 characters and include letters and numbers.', 'danger');
    elseif ($new !== '' && $new !== ($_POST['confirm'] ?? '')) flash('New passwords do not match.', 'danger');
    elseif ($new === '' && !empty($a['must_change'])) flash('Please choose a new password.', 'danger');
    else {
        $a['username'] = $user; if ($new !== '') $a['hash'] = password_hash($new, PASSWORD_DEFAULT);
        $a['must_change'] = 0; store_write('admin', $a); $_SESSION['uid'] = $user; session_regenerate_id(true);
        flash('Account updated.'); redirect(aurl('account.php'));
    }
    redirect(aurl('account.php'));
}
admin_head('My Account', 'account.php'); ?>
<div class="card-box" style="max-width:640px">
  <?php if (!empty($a['must_change'])): ?><div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> You are using the default password. Choose a new one now.</div><?php endif; ?>
  <form method="post"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" value="<?= e($a['username']) ?>" required></div>
    <div class="mb-3"><label class="form-label">Current password</label><input class="form-control" type="password" name="current" required autocomplete="current-password"></div>
    <div class="row"><div class="col-md-6 mb-3"><label class="form-label">New password</label><input class="form-control" type="password" name="new" autocomplete="new-password"><div class="form-text">Min. 10 characters, letters + numbers.</div></div>
    <div class="col-md-6 mb-3"><label class="form-label">Confirm new password</label><input class="form-control" type="password" name="confirm" autocomplete="new-password"></div></div>
    <button class="btn btn-brand">Save account</button>
  </form>
</div>
<?php admin_foot();
