<?php
require_once dirname(__DIR__) . '/includes/mailer.php';
if (session_status() === PHP_SESSION_NONE) {
    session_name('dialadm');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

function aurl($p = '') { return base_path() . '/admin/' . $p; }
function admin_data() { return store_read('admin', []); }
function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field() { return '<input type="hidden" name="_csrf" value="' . e(csrf()) . '">'; }
function csrf_check() {
    $t = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF'] ?? '');
    if (!$t || !hash_equals(csrf(), $t)) { http_response_code(419); exit('Session expired. Go back, refresh the page and try again.'); }
}
function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'][] = [$type, $msg]; return; }
    $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f;
}
function redirect($to) { header('Location: ' . $to); exit; }
function is_post() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

function require_login($allowMustChange = false) {
    $a = admin_data();
    if (empty($_SESSION['uid']) || empty($a) || $_SESSION['uid'] !== $a['username']) redirect(aurl('login.php'));
    if (!empty($a['must_change']) && !$allowMustChange) { flash('For your security, please set a new password before continuing.', 'warning'); redirect(aurl('account.php')); }
}

function nav_items() {
    return [
        'index.php' => ['Dashboard', 'bi-speedometer2'],
        'content.php' => ['Pages & Content', 'bi-file-earmark-richtext'],
        'seo.php' => ['SEO', 'bi-search'],
        'media.php' => ['Media Library', 'bi-images'],
        'leads.php' => ['Messages', 'bi-inbox'],
        'smtp.php' => ['SMTP / Email', 'bi-envelope-at'],
        'settings.php' => ['Site Settings', 'bi-sliders'],
        'account.php' => ['My Account', 'bi-person-lock'],
    ];
}
function unread_leads() { $n = 0; foreach (store_read('leads', []) as $l) if (empty($l['read'])) $n++; return $n; }

function admin_head($title, $active) {
    $unread = unread_leads(); ?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title><?= e($title) ?> · Dashboard</title>
<link rel="icon" href="<?= e(asset(setting('favicon') ?: 'assets/img/favicon.svg')) ?>">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('assets/vendor/icons/bootstrap-icons.min.css') ?>">
<link rel="stylesheet" href="<?= aurl('assets/admin.css') ?>?v=1">
<style>:root{--brand:<?= e(hex_ok2(setting('color_primary'), '#0287BF')) ?>;--ink:<?= e(hex_ok2(setting('color_dark'), '#101828')) ?>}</style>
</head><body data-csrf="<?= e(csrf()) ?>" data-media="<?= e(aurl('media.php')) ?>" data-base="<?= e(base_path()) ?>">
<div class="adm">
  <aside class="side" id="side">
    <a class="side-brand" href="<?= aurl() ?>"><img src="<?= e(asset(setting('logo') ?: 'assets/img/logo.svg')) ?>" height="26" alt=""><span><?= e(setting('site_name')) ?></span></a>
    <nav>
      <?php foreach (nav_items() as $f => [$l, $i]): ?>
        <a class="<?= $active === $f ? 'on' : '' ?>" href="<?= aurl($f) ?>"><i class="bi <?= $i ?>"></i><span><?= e($l) ?></span><?php if ($f === 'leads.php' && $unread): ?><b class="pill"><?= $unread ?></b><?php endif; ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="side-foot">
      <a href="<?= url('') ?>" target="_blank"><i class="bi bi-box-arrow-up-right"></i><span>View website</span></a>
      <a href="<?= aurl('logout.php') ?>"><i class="bi bi-box-arrow-left"></i><span>Log out</span></a>
    </div>
  </aside>
  <div class="main">
    <header class="top"><button class="burger" onclick="document.getElementById('side').classList.toggle('open')"><i class="bi bi-list"></i></button><h1><?= e($title) ?></h1><span class="who"><i class="bi bi-person-circle"></i> <?= e($_SESSION['uid'] ?? '') ?></span></header>
    <div class="content">
    <?php foreach (flash() as [$t, $m]): ?><div class="alert alert-<?= $t ?> alert-dismissible fade show"><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endforeach; ?>
<?php }
function hex_ok2($c, $d) { return preg_match('/^#[0-9a-fA-F]{6}$/', (string)$c) ? $c : $d; }

function admin_foot() { ?>
    </div>
  </div>
</div>
<!-- Media picker modal -->
<div class="modal fade" id="mediaModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Choose an image</h5>
    <label class="btn btn-brand btn-sm ms-auto me-3 mb-0"><i class="bi bi-upload"></i> Upload new<input type="file" id="pickUpload" accept="image/*" hidden multiple></label>
    <button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body"><div id="pickGrid" class="media-grid"></div></div>
</div></div></div>
<script src="<?= asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= aurl('assets/admin.js') ?>?v=1"></script>
</body></html>
<?php }

/* ---------- generic form-field renderer ---------- */
function render_field($name, $f, $val, $idPrefix = '') {
    $id = 'f_' . substr(md5($name), 0, 10);
    $hint = !empty($f['hint']) ? '<div class="form-text">' . e($f['hint']) . '</div>' : '';
    $label = '<label class="form-label" for="' . $id . '">' . e($f['label']) . '</label>';
    switch ($f['type']) {
        case 'textarea': return '<div class="mb-3">' . $label . '<textarea class="form-control" id="' . $id . '" name="' . e($name) . '" rows="3">' . e($val) . '</textarea>' . $hint . '</div>';
        case 'html': return '<div class="mb-3">' . $label . '<textarea class="form-control code" id="' . $id . '" name="' . e($name) . '" rows="10" spellcheck="false">' . e($val) . '</textarea>' . $hint . '</div>';
        case 'toggle': return '<div class="mb-3 form-check form-switch"><input type="checkbox" class="form-check-input" role="switch" id="' . $id . '" name="' . e($name) . '" value="1"' . ($val ? ' checked' : '') . '><label class="form-check-label" for="' . $id . '">' . e($f['label']) . '</label>' . $hint . '</div>';
        case 'select':
            $o = ''; foreach ($f['options'] as $k => $l) $o .= '<option value="' . e($k) . '"' . ((string)$val === (string)$k ? ' selected' : '') . '>' . e($l) . '</option>';
            return '<div class="mb-3">' . $label . '<select class="form-select" id="' . $id . '" name="' . e($name) . '">' . $o . '</select>' . $hint . '</div>';
        case 'color': return '<div class="mb-3">' . $label . '<div class="color-wrap"><input type="color" class="form-control form-control-color" value="' . e($val) . '" oninput="this.nextElementSibling.value=this.value"><input class="form-control" id="' . $id . '" name="' . e($name) . '" value="' . e($val) . '" maxlength="7" oninput="if(/^#[0-9a-f]{6}$/i.test(this.value))this.previousElementSibling.value=this.value"></div>' . $hint . '</div>';
        case 'password': return '<div class="mb-3">' . $label . '<input type="password" class="form-control" id="' . $id . '" name="' . e($name) . '" value="" autocomplete="new-password" placeholder="' . ($val ? '•••••••• (saved)' : '') . '">' . $hint . '</div>';
        case 'image':
            $src = $val ? asset($val) : '';
            return '<div class="mb-3 imgfield">' . $label . '<div class="input-group"><span class="thumb">' . ($src ? '<img src="' . e($src) . '" alt="">' : '<i class="bi bi-image"></i>') . '</span><input class="form-control" id="' . $id . '" name="' . e($name) . '" value="' . e($val) . '" placeholder="uploads/image.webp"><button type="button" class="btn btn-outline-secondary" data-pick>Choose</button><button type="button" class="btn btn-outline-secondary" data-clear title="Clear"><i class="bi bi-x-lg"></i></button></div>' . $hint . '</div>';
        case 'list':
            $h = '<div class="mb-4 listfield" data-name="' . e($name) . '"><div class="d-flex align-items-center mb-2"><label class="form-label mb-0 fw-semibold">' . e($f['label']) . '</label><button type="button" class="btn btn-sm btn-brand ms-auto" data-add><i class="bi bi-plus-lg"></i> Add item</button></div>' . $hint . '<div class="list-rows">';
            $vals = is_array($val) ? array_values($val) : [];
            foreach ($vals as $i => $row) $h .= render_list_row($name, $f, $i, $row);
            $h .= '</div><template>' . render_list_row($name, $f, '__IDX__', []) . '</template></div>';
            return $h;
        default: return '<div class="mb-3">' . $label . '<input class="form-control" id="' . $id . '" name="' . e($name) . '" value="' . e($val) . '">' . $hint . '</div>';
    }
}
function render_list_row($name, $f, $i, $row) {
    $title = ''; foreach ($f['item'] as $k => $ff) if ($ff['type'] === 'text' && !empty($row[$k])) { $title = $row[$k]; break; }
    $h = '<div class="list-row"><div class="lr-head"><i class="bi bi-grip-vertical"></i><span class="lr-title">' . e($title ?: 'New item') . '</span><span class="ms-auto"><button type="button" class="btn btn-sm btn-light" data-up title="Move up"><i class="bi bi-arrow-up"></i></button> <button type="button" class="btn btn-sm btn-light" data-down title="Move down"><i class="bi bi-arrow-down"></i></button> <button type="button" class="btn btn-sm btn-light text-danger" data-del title="Delete"><i class="bi bi-trash"></i></button></span></div><div class="lr-body row g-0">';
    foreach ($f['item'] as $k => $ff) {
        $wide = in_array($ff['type'], ['textarea', 'image', 'html', 'list']) ? 'col-12' : 'col-md-6';
        $h .= '<div class="' . $wide . ' px-2">' . render_field($name . '[' . $i . '][' . $k . ']', $ff, $row[$k] ?? ($ff['type'] === 'toggle' ? 0 : '')) . '</div>';
    }
    return $h . '</div></div>';
}
function render_fields($prefix, $fields, $values) {
    $h = '';
    foreach ($fields as $k => $f) $h .= render_field($prefix . '[' . $k . ']', $f, $values[$k] ?? ($f['default'] ?? ''));
    return $h;
}
