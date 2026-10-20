<?php
require_once __DIR__ . '/_boot.php';
require_login();
$groups = settings_schema();
if (is_post()) {
    csrf_check();
    $out = [];
    foreach ($groups as $g) $out += collect_fields($g['fields'], $_POST['f'] ?? []);
    foreach (['color_primary' => '#0287BF', 'color_bg' => '#F5F7FB', 'color_dark' => '#101828'] as $k => $d) if (!preg_match('/^#[0-9a-fA-F]{6}$/', $out[$k] ?? '')) $out[$k] = $d;
    $out['site_url'] = rtrim($out['site_url'], '/');
    store_write('settings', $out);
    flash('Settings saved.'); redirect(aurl('settings.php') . '#' . ($_POST['_tab'] ?? ''));
}
$cur = all_settings();
admin_head('Site Settings', 'settings.php'); ?>
<form method="post"><?= csrf_field() ?><input type="hidden" name="_tab" id="tabKey">
  <div class="sticky-bar"><div><strong>Site settings</strong> <span class="text-muted">— logo, colours, menu, footer, contact info, scripts.</span></div><button class="btn btn-brand"><i class="bi bi-check2"></i> Save settings</button></div>
  <ul class="nav nav-pills page-tabs mb-4" role="tablist">
    <?php $i = 0; foreach ($groups as $k => $g): ?><li class="nav-item"><button type="button" class="nav-link <?= $i++ ? '' : 'active' ?>" data-bs-toggle="pill" data-bs-target="#t-<?= $k ?>" data-tab="<?= $k ?>"><i class="bi <?= $g['icon'] ?>"></i> <?= e($g['label']) ?></button></li><?php endforeach; ?>
  </ul>
  <div class="tab-content">
    <?php $i = 0; foreach ($groups as $k => $g): ?>
    <div class="tab-pane fade <?= $i++ ? '' : 'show active' ?>" id="t-<?= $k ?>"><div class="card-box"><?= render_fields('f', $g['fields'], $cur) ?></div></div>
    <?php endforeach; ?>
  </div>
</form>
<?php admin_foot();
