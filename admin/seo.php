<?php
require_once __DIR__ . '/_boot.php';
require_login();
$pages = pages_schema(); $fields = seo_fields();
if (is_post()) {
    csrf_check();
    $saved = [];
    foreach ($pages as $k => $p) $saved[$k] = collect_fields($fields, $_POST['f'][$k] ?? []);
    store_write('seo', $saved);
    flash('SEO settings saved.'); redirect(aurl('seo.php'));
}
admin_head('SEO', 'seo.php'); ?>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card-box h-100"><div class="small-h">Sitemap</div><a href="<?= e(site_origin()) ?>/sitemap.xml" target="_blank"><?= e(site_origin()) ?>/sitemap.xml</a><div class="form-text">Auto-generated. Submit it in Google Search Console.</div></div></div>
  <div class="col-md-4"><div class="card-box h-100"><div class="small-h">robots.txt</div><a href="<?= e(site_origin()) ?>/robots.txt" target="_blank"><?= e(site_origin()) ?>/robots.txt</a><div class="form-text">Auto-generated.</div></div></div>
  <div class="col-md-4"><div class="card-box h-100"><div class="small-h">Global SEO, verification & analytics</div><a href="settings.php#seo">Site Settings → Global SEO</a> · <a href="settings.php#code">Scripts</a></div></div>
</div>
<form method="post"><?= csrf_field() ?>
  <div class="sticky-bar"><div><strong>Per-page SEO</strong> <span class="text-muted">— title, description, social image, indexing.</span></div><button class="btn btn-brand"><i class="bi bi-check2"></i> Save SEO</button></div>
  <div class="accordion sec-acc" id="seoAcc">
    <?php $n = 0; foreach ($pages as $k => $p): $n++; $v = seo_for($k); ?>
    <div class="accordion-item">
      <h2 class="accordion-header"><button class="accordion-button <?= $n > 1 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#s-<?= $k ?>"><span class="num"><i class="bi <?= $p['icon'] ?>"></i></span><?= e($p['label']) ?><small class="ms-2 text-muted">/<?= e($p['slug']) ?></small></button></h2>
      <div id="s-<?= $k ?>" class="accordion-collapse collapse <?= $n === 1 ? 'show' : '' ?>"><div class="accordion-body seo-block">
        <div class="serp mb-3"><div class="serp-url"><?= e(site_origin()) ?>/<?= e($p['slug']) ?></div><div class="serp-title" data-serp-title>…</div><div class="serp-desc" data-serp-desc>…</div></div>
        <?= render_fields("f[$k]", $fields, $v) ?>
      </div></div>
    </div>
    <?php endforeach; ?>
  </div>
</form>
<?php admin_foot();
