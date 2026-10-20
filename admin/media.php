<?php
require_once __DIR__ . '/_boot.php';
require_login();
$ok_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'ico', 'avif'];
function media_list() {
    $out = [];
    foreach (glob(UPLOAD_DIR . '/*') as $f) {
        if (!is_file($f)) continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'ico', 'avif'])) continue;
        $out[] = ['name' => basename($f), 'path' => 'uploads/' . basename($f), 'url' => asset('uploads/' . basename($f)), 'size' => filesize($f), 'time' => filemtime($f)];
    }
    usort($out, fn($a, $b) => $b['time'] <=> $a['time']);
    return $out;
}
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if ($action === 'list') json_out(['files' => media_list()]);
if (is_post()) {
    csrf_check();
    if ($action === 'upload') {
        $saved = []; $errors = [];
        $files = $_FILES['files'] ?? null;
        if ($files) foreach ((array)$files['name'] as $i => $nm) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) { $errors[] = "$nm: upload error " . $files['error'][$i]; continue; }
            $ext = strtolower(pathinfo($nm, PATHINFO_EXTENSION));
            if (!in_array($ext, $ok_ext)) { $errors[] = "$nm: file type not allowed"; continue; }
            if ($files['size'][$i] > 12 * 1024 * 1024) { $errors[] = "$nm: larger than 12 MB"; continue; }
            if ($ext !== 'svg' && $ext !== 'ico' && !@getimagesize($files['tmp_name'][$i])) { $errors[] = "$nm: not a valid image"; continue; }
            if ($ext === 'svg') { $c = file_get_contents($files['tmp_name'][$i]); if (preg_match('/<script|onload=|onerror=|javascript:/i', $c)) { $errors[] = "$nm: SVG contains scripts"; continue; } }
            $base = slugify(pathinfo($nm, PATHINFO_FILENAME)); $name = $base . '.' . $ext; $n = 1;
            while (file_exists(UPLOAD_DIR . '/' . $name)) $name = $base . '-' . (++$n) . '.' . $ext;
            if (move_uploaded_file($files['tmp_name'][$i], UPLOAD_DIR . '/' . $name)) { @chmod(UPLOAD_DIR . '/' . $name, 0644); $saved[] = ['name' => $name, 'path' => 'uploads/' . $name, 'url' => asset('uploads/' . $name)]; }
            else $errors[] = "$nm: could not save (check uploads/ folder permissions)";
        }
        if (!empty($_SERVER['HTTP_X_CSRF'])) json_out(['ok' => !$errors || $saved, 'saved' => $saved, 'errors' => $errors]);
        if ($saved) flash(count($saved) . ' file(s) uploaded.'); foreach ($errors as $er) flash($er, 'danger');
        redirect(aurl('media.php'));
    }
    if ($action === 'delete') {
        $n = basename($_POST['name'] ?? ''); $f = UPLOAD_DIR . '/' . $n;
        if ($n && is_file($f)) { unlink($f); flash("Deleted $n."); }
        redirect(aurl('media.php'));
    }
}
$files = media_list();
admin_head('Media Library', 'media.php'); ?>
<div class="card-box mb-4">
  <form method="post" enctype="multipart/form-data" class="d-flex flex-wrap gap-3 align-items-center"><?= csrf_field() ?><input type="hidden" name="action" value="upload">
    <input class="form-control" style="max-width:420px" type="file" name="files[]" accept="image/*" multiple required>
    <button class="btn btn-brand"><i class="bi bi-upload"></i> Upload images</button>
    <span class="text-muted small">JPG, PNG, WebP, GIF, SVG · up to 12 MB each. Tip: WebP keeps pages fast.</span>
  </form>
</div>
<div class="media-grid">
  <?php foreach ($files as $f): ?>
  <div class="media-item">
    <div class="mi-img"><img src="<?= e($f['url']) ?>" alt="" loading="lazy"></div>
    <div class="mi-meta"><div class="mi-name" title="<?= e($f['name']) ?>"><?= e($f['name']) ?></div><small><?= round($f['size'] / 1024) ?> KB</small></div>
    <div class="mi-act">
      <button class="btn btn-sm btn-light" data-copy="<?= e($f['path']) ?>"><i class="bi bi-clipboard"></i> Copy path</button>
      <form method="post" onsubmit="return confirm('Delete <?= e($f['name']) ?>? Pages using it will show a broken image.')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="name" value="<?= e($f['name']) ?>"><button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button></form>
    </div>
  </div>
  <?php endforeach; if (!$files): ?><p class="text-muted">No images yet.</p><?php endif; ?>
</div>
<?php admin_foot();
