<?php
function feature_rows($items, $bg = false) {
    foreach ($items as $i => $it): $flip = $i % 2 === 1; ?>
<section class="section feature-row <?= $bg ? 'bg-soft' : '' ?>">
  <div class="container">
    <div class="row align-items-center g-5 <?= $flip ? 'flex-lg-row-reverse' : '' ?>">
      <div class="col-lg-5 reveal">
        <?php if ($it['tag']): ?><span class="eyebrow"><i></i><?= e($it['tag']) ?></span><?php endif; ?>
        <h2 class="h1-sm"><?= rich($it['title']) ?></h2>
        <p class="lead-sm"><?= e($it['text']) ?></p>
        <?php if (!empty($it['bullets'])): ?><ul class="check-list"><?php foreach (lines($it['bullets']) as $b): ?><li><i class="bi bi-check-circle-fill"></i><?= e($b) ?></li><?php endforeach; ?></ul><?php endif; ?>
      </div>
      <div class="col-lg-7 reveal">
        <div class="shot<?= strpos($it['image'], 'crop') !== false ? ' narrow' : '' ?>"><img src="<?= e(asset($it['image'])) ?>" alt="<?= e(strip_tags($it['title'])) ?>" loading="lazy"></div>
      </div>
    </div>
  </div>
</section>
<?php endforeach; }

function icon_cards($items, $cols = 'col-md-6 col-lg-3') {
    echo '<div class="row g-4">';
    foreach ($items as $it): ?>
  <div class="<?= $cols ?> reveal"><div class="icon-card"><div class="ic"><i class="bi <?= e($it['icon'] ?: 'bi-stars') ?>"></i></div><h3><?= e($it['title']) ?></h3><p><?= e($it['text']) ?></p></div></div>
<?php endforeach; echo '</div>'; }

function section_title($title, $text = '', $eyebrow = '') { ?>
<div class="section-title reveal">
  <?php if ($eyebrow): ?><span class="eyebrow"><i></i><?= e($eyebrow) ?></span><?php endif; ?>
  <h2><?= rich($title) ?></h2>
  <?php if ($text): ?><p><?= e($text) ?></p><?php endif; ?>
</div>
<?php }
