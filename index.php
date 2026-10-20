<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/blocks.php';
$P = 'home'; $h = page_content($P);
site_head($P, 'page-home');
$hero = $h['hero'];
?>
<section class="hero">
  <div class="hero-bg"></div>
  <div class="container position-relative">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6">
        <?php if ($hero['eyebrow']): ?><span class="eyebrow"><i></i><?= e($hero['eyebrow']) ?></span><?php endif; ?>
        <h1 class="hero-title"><?= rich($hero['title']) ?></h1>
        <p class="hero-text"><?= e($hero['text']) ?></p>
        <div class="d-flex flex-wrap gap-2">
          <?php if ($hero['cta1_text']): ?><a class="btn btn-brand btn-lg-pill" href="<?= e(href($hero['cta1_link'])) ?>"><?= e($hero['cta1_text']) ?></a><?php endif; ?>
          <?php if ($hero['cta2_text']): ?><a class="btn btn-ghost btn-lg-pill" href="<?= e(href($hero['cta2_link'])) ?>"><?= e($hero['cta2_text']) ?></a><?php endif; ?>
        </div>
        <?php if ($hero['note']): ?><p class="hero-note"><i class="bi bi-shield-check"></i> <?= e($hero['note']) ?></p><?php endif; ?>
        <?php store_buttons('mt-3'); ?>
      </div>
      <div class="col-lg-6">
        <div class="hero-visual">
          <div class="chip chip-call" id="chipCall" aria-hidden="true">
            <span class="chip-av"><i class="bi bi-telephone-fill"></i><b></b><b></b></span>
            <span class="chip-tx"><small id="ccState">Incoming call</small><strong><?= e(setting('site_name')) ?></strong></span>
            <span class="eq" id="ccEq"><i></i><i></i><i></i><i></i><i></i></span>
          </div>
          <div class="chip chip-sms" aria-hidden="true">
            <span class="chip-av chip-g"><i class="bi bi-chat-dots-fill"></i></span>
            <span class="chip-tx"><small>New SMS</small><strong>Delivered <i class="bi bi-check2-all"></i></strong></span>
          </div>
          <div class="shot shot-main"><img src="<?= e(asset($hero['img_main'])) ?>" alt="Dial Infini messages screen"></div>
          <?php if ($hero['img_phone1']): ?><div class="phone phone-l"><img src="<?= e(asset($hero['img_phone1'])) ?>" alt="Dial pad screen"></div><?php endif; ?>
          <?php if ($hero['img_phone2']): ?><div class="phone phone-r"><img src="<?= e(asset($hero['img_phone2'])) ?>" alt="Call anyone screen"></div><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php $s = $h['stats']; ?>
<section class="section statement">
  <div class="container">
    <?php if ($s['kicker']): ?><span class="eyebrow reveal"><i></i><?= e($s['kicker']) ?></span><?php endif; ?>
    <h2 class="statement-title scrub" aria-label="<?= e(strip_tags(str_replace('*', '', $s['title']))) ?>"><?= rich($s['title']) ?></h2>
    <div class="stat-row">
      <?php foreach ($s['items'] as $it): ?>
      <div class="stat-item reveal">
        <div class="stat-v"><?= e($it['value']) ?></div>
        <p><?= e($it['label']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php $mq = array_merge(array_column($h['usecases']['items'], 'title'), array_column($h['products']['items'], 'title')); if ($mq): ?>
<div class="marquee" aria-hidden="true"><div class="marquee-track"><?php for ($k = 0; $k < 2; $k++) foreach ($mq as $w): ?><span><i class="bi bi-asterisk"></i><?= e($w) ?></span><?php endforeach; ?></div></div>
<?php endif; ?>

<?php $s = $h['products']; ?>
<section class="section products-sec" id="products">
  <div class="container">
    <?php section_title($s['title'], $s['text']); ?>
    <div class="stack">
      <?php foreach ($s['items'] as $n => $it): ?>
      <a class="scard pc-<?= $n % 4 ?>" style="--i:<?= $n ?>" href="<?= e(href($it['link'])) ?>">
        <div class="scard-txt">
          <span class="scard-no">0<?= $n + 1 ?> <em>/ 0<?= count($s['items']) ?></em></span>
          <span class="tag"><?= e($it['tag']) ?></span>
          <h3><?= e($it['title']) ?></h3>
          <p><?= e($it['text']) ?></p>
          <span class="btn btn-brand btn-sm-pill"><?= e($it['link_text'] ?: 'Learn more') ?> <i class="bi bi-arrow-right"></i></span>
        </div>
        <div class="scard-media"><span class="pc-blob"></span><div class="phone phone-sm"><img src="<?= e(asset($it['image'])) ?>" alt="" loading="lazy"></div></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php feature_rows($h['showcase']['items']); ?>

<?php $s = $h['how']; ?>
<section class="section bg-soft" id="how">
  <div class="container">
    <?php section_title($s['title'], $s['text'], 'How it works'); ?>
    <div class="loop-track" id="loopTrack"><span class="lt-bar"><i></i></span><?php foreach ($s['items'] as $i => $it): ?><span class="lt-node"><b><?= $i + 1 ?></b><em><?= e($it['title']) ?></em></span><?php endforeach; ?></div>
    <div class="row g-4" id="loopSteps">
      <?php foreach ($s['items'] as $i => $it): ?>
      <div class="col-sm-6 col-lg-3 reveal">
        <div class="step lp">
          <div class="phone phone-step"><img src="<?= e(asset($it['image'])) ?>" alt="" loading="lazy"></div>
          <div class="step-n"><?= $i + 1 ?></div>
          <h3><?= e($it['title']) ?></h3>
          <p><?= e($it['text']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php $s = $h['usecases']; ?>
<section class="section">
  <div class="container">
    <?php section_title($s['title']); icon_cards($s['items']); ?>
  </div>
</section>

<?php $s = $h['pricing']; ?>
<section class="section dark-section">
  <div class="container">
    <div class="section-title reveal on-dark"><h2><?= rich($s['title']) ?></h2><p><?= e($s['text']) ?></p></div>
    <div class="row g-4 justify-content-center">
      <?php foreach ($s['items'] as $it): ?>
      <div class="col-md-4 reveal"><div class="rate-card"><div class="rate-v"><?= e($it['value']) ?><small><?= e($it['unit']) ?></small></div><h3><?= e($it['title']) ?></h3><p><?= e($it['text']) ?></p></div></div>
      <?php endforeach; ?>
    </div>
    <?php if ($s['cta_text']): ?><div class="text-center mt-5"><a class="btn btn-brand btn-lg-pill" href="<?= e(href($s['cta_link'])) ?>"><?= e($s['cta_text']) ?> <i class="bi bi-arrow-right"></i></a></div><?php endif; ?>
  </div>
</section>

<?php $s = $h['cta']; $section_gap = true; ?>
<div class="pt-5"></div>
<?php cta_band($s['title'], $s['text'], $s['cta1_text'], $s['cta1_link'], $s['cta2_text'], $s['cta2_link'], $s['image']); ?>
<?php site_foot();
