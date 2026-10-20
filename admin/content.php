<?php
require_once __DIR__ . '/_boot.php';
require_login();
$pages = pages_schema();
$cur = $_GET['page'] ?? 'home'; if (!isset($pages[$cur])) $cur = 'home';
if (is_post()) {
    csrf_check();
    $saved = store_read('content');
    if (($_POST['action'] ?? '') === 'reset') { unset($saved[$cur]); store_write('content', $saved); flash('Page reset to default content.'); redirect(aurl('content.php?page=' . $cur)); }
    foreach ($pages[$cur]['sections'] as $sk => $sec) $saved[$cur][$sk] = collect_fields($sec['fields'], $_POST['f'][$sk] ?? []);
    store_write('content', $saved);
    flash('“' . $pages[$cur]['label'] . '” saved. Changes are live.');
    redirect(aurl('content.php?page=' . $cur . '#' . ($_POST['_open'] ?? '')));
}
$vals = page_content($cur);
admin_head('Pages & Content', 'content.php'); ?>
<ul class="nav nav-pills page-tabs mb-4">
  <?php foreach ($pages as $k => $p): ?><li class="nav-item"><a class="nav-link <?= $k === $cur ? 'active' : '' ?>" href="?page=<?= $k ?>"><i class="bi <?= $p['icon'] ?>"></i> <?= e($p['label']) ?></a></li><?php endforeach; ?>
</ul>
<form method="post" id="editForm"><?= csrf_field() ?><input type="hidden" name="_open" id="openSec">
  <div class="sticky-bar">
    <div><strong><?= e($pages[$cur]['label']) ?></strong> <span class="text-muted">— edit text, links and images, then save.</span></div>
    <div class="d-flex gap-2">
      <a class="btn btn-light" href="<?= $cur === 'home' ? url('') : url($pages[$cur]['slug']) ?>" target="_blank"><i class="bi bi-eye"></i> View page</a>
      <button class="btn btn-brand"><i class="bi bi-check2"></i> Save changes</button>
    </div>
  </div>
  <div class="accordion sec-acc" id="secAcc">
    <?php $n = 0; foreach ($pages[$cur]['sections'] as $sk => $sec): $n++; ?>
    <div class="accordion-item" id="sec-<?= $sk ?>">
      <h2 class="accordion-header"><button class="accordion-button <?= $n > 1 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#c-<?= $sk ?>" data-key="<?= $sk ?>"><span class="num"><?= $n ?></span><?= e($sec['label']) ?></button></h2>
      <div id="c-<?= $sk ?>" class="accordion-collapse collapse <?= $n === 1 ? 'show' : '' ?>"><div class="accordion-body"><?= render_fields("f[$sk]", $sec['fields'], $vals[$sk] ?? []) ?></div></div>
    </div>
    <?php endforeach; ?>
  </div>
</form>
<form method="post" class="mt-4" onsubmit="return confirm('Reset ALL content of this page to the original text and images?')"><?= csrf_field() ?><input type="hidden" name="action" value="reset"><button class="btn btn-link text-danger p-0"><i class="bi bi-arrow-counterclockwise"></i> Reset this page to default content</button></form>
<?php admin_foot();
