<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/blocks.php';
$P = 'about'; $h = page_content($P);
site_head($P, 'page-inner');
page_hero($h['hero']);
$s = $h['story']; ?>
<section class="section pt-0">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <h2 class="h1-sm"><?= rich($s['title']) ?></h2>
        <?php foreach (preg_split('/\n\s*\n/', $s['text']) as $para): ?><p class="lead-sm"><?= nl(trim($para)) ?></p><?php endforeach; ?>
      </div>
      <div class="col-lg-6 reveal"><div class="shot"><img src="<?= e(asset($s['image'])) ?>" alt="" loading="lazy"></div></div>
    </div>
  </div>
</section>
<?php $v = $h['values']; ?>
<section class="section bg-soft"><div class="container"><?php section_title($v['title']); icon_cards($v['items']); ?></div></section>
<div class="pt-5"></div>
<?php $c = $h['cta']; cta_band($c['title'], $c['text'], $c['cta_text'], $c['cta_link']);
site_foot();
