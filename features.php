<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/blocks.php';
$P = 'features'; $h = page_content($P);
site_head($P, 'page-inner');
page_hero($h['hero']);
feature_rows($h['blocks']['items'], false);
$g = $h['grid']; ?>
<section class="section bg-soft">
  <div class="container"><?php section_title($g['title']); icon_cards($g['items'], 'col-md-6 col-lg-4'); ?></div>
</section>
<div class="pt-5"></div>
<?php $c = $h['cta']; cta_band($c['title'], $c['text'], $c['cta_text'], $c['cta_link']);
site_foot();
