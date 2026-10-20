<?php
require_once __DIR__ . '/bootstrap.php';

function hex_ok($c, $d) { return preg_match('/^#[0-9a-fA-F]{6}$/', (string)$c) ? $c : $d; }
function shade($hex, $pct) { // lighten (+) / darken (-)
    $hex = ltrim($hex, '#'); $o = '#';
    foreach ([0, 2, 4] as $i) { $v = hexdec(substr($hex, $i, 2)); $v = max(0, min(255, round($pct >= 0 ? $v + (255 - $v) * $pct : $v * (1 + $pct)))); $o .= str_pad(dechex($v), 2, '0', STR_PAD_LEFT); }
    return $o;
}

/** All emails / phones / addresses (main + extras) as [[label,value],...]. $type: email|phone|address */
function contact_entries($type) {
    $out = [];
    if (trim((string)setting($type))) $out[] = ['', trim(setting($type))];
    foreach ((setting('more_' . $type . ($type === 'address' ? 'es' : 's')) ?: []) as $r) {
        $v = trim((string)($r['value'] ?? ''));
        if ($v !== '') $out[] = [trim((string)($r['label'] ?? '')), $v];
    }
    return $out;
}
/** Renders <li> rows for the footer ($dark=false) or the contact card. */
function contact_rows($ctx = 'footer') {
    $map = ['email' => ['bi-envelope', 'Email'], 'phone' => ['bi-telephone', 'Phone'], 'address' => ['bi-geo-alt', 'Address']];
    foreach ($map as $type => [$ic, $def]) foreach (contact_entries($type) as [$label, $v]) {
        if ($type === 'email') $val = '<a href="mailto:' . e($v) . '">' . e($v) . '</a>';
        elseif ($type === 'phone') $val = '<a href="tel:' . e(preg_replace('/[^+\d]/', '', $v)) . '">' . e($v) . '</a>';
        else $val = nl($v);
        if ($ctx === 'contact') echo '<li><i class="bi ' . $ic . '"></i><div><small>' . e($label ?: $def) . '</small>' . $val . '</div></li>';
        else echo '<li><i class="bi ' . $ic . '"></i><span>' . ($label ? '<small class="fc-label">' . e($label) . '</small>' : '') . $val . '</span></li>';
    }
}
function icon_link($name) { $m = ['facebook' => 'bi-facebook', 'instagram' => 'bi-instagram', 'x_twitter' => 'bi-twitter-x', 'linkedin' => 'bi-linkedin', 'youtube' => 'bi-youtube']; return $m[$name] ?? 'bi-link'; }

function site_head($page, $bodyClass = '') {
    $seo = seo_for($page);
    $schema = pages_schema()[$page] ?? [];
    $name = setting('site_name');
    $title = $seo['title'] ?: ($schema['label'] ?? $name);
    $suffix = setting('title_suffix');
    $fullTitle = (stripos($title, $name) !== false) ? $title : $title . $suffix;
    $origin = site_origin();
    $path = ($page === 'home') ? '/' : '/' . ($schema['slug'] ?? $page);
    $canonical = $seo['canonical'] ?: $origin . (setting('clean_urls') ? $path : ($page === 'home' ? '/' : $path . '.php'));
    $og = $seo['og_image'] ?: setting('default_og');
    $ogUrl = $og ? (preg_match('#^https?://#', $og) ? $og : $origin . '/' . ltrim($og, '/')) : '';
    $p = hex_ok(setting('color_primary'), '#0287BF'); $bg = hex_ok(setting('color_bg'), '#F5F7FB'); $dk = hex_ok(setting('color_dark'), '#101828');
    $fav = setting('favicon') ?: 'assets/img/favicon.svg';
    $ver = '4.3';
    ?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($seo['description']) ?>">
<?php if ($seo['keywords']): ?><meta name="keywords" content="<?= e($seo['keywords']) ?>">
<?php endif; ?>
<meta name="robots" content="<?= !empty($GLOBALS['__404']) ? 'noindex,follow' : e($seo['robots'] ?: 'index,follow') ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<?php if (setting('google_verification')): ?><meta name="google-site-verification" content="<?= e(setting('google_verification')) ?>">
<?php endif; if (setting('bing_verification')): ?><meta name="msvalidate.01" content="<?= e(setting('bing_verification')) ?>">
<?php endif; ?>
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($name) ?>">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:description" content="<?= e($seo['description']) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<?php if ($ogUrl): ?><meta property="og:image" content="<?= e($ogUrl) ?>">
<meta name="twitter:image" content="<?= e($ogUrl) ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($fullTitle) ?>">
<meta name="twitter:description" content="<?= e($seo['description']) ?>">
<?php if (setting('twitter_handle')): ?><meta name="twitter:site" content="<?= e(setting('twitter_handle')) ?>">
<?php endif; ?>
<meta name="theme-color" content="<?= e($p) ?>">
<link rel="icon" href="<?= e(asset($fav)) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('assets/vendor/icons/bootstrap-icons.min.css') ?>">
<link rel="stylesheet" href="<?= asset('assets/css/site.css') ?>?v=<?= $ver ?>">
<link rel="stylesheet" href="<?= asset('assets/css/premium.css') ?>?v=<?= $ver ?>">
<link rel="stylesheet" href="<?= asset('assets/css/dialinfini.css') ?>?v=<?= $ver ?>">
<style>:root{--brand:<?= $p ?>;--brand-dark:<?= shade($p, -0.18) ?>;--brand-light:<?= shade($p, 0.35) ?>;--brand-soft:<?= shade($p, 0.9) ?>;--bg:<?= $bg ?>;--ink:<?= $dk ?>;}<?= setting('custom_css') ?></style>
<?php if (setting('schema_on')): ?>
<script type="application/ld+json"><?= json_encode(array_filter(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $name, 'url' => $origin . '/', 'logo' => $origin . '/' . ltrim(setting('logo') ?: 'assets/img/logo.svg', '/'), 'email' => setting('email'), 'telephone' => setting('phone'), 'sameAs' => array_values(array_filter([setting('facebook'), setting('instagram'), setting('x_twitter'), setting('linkedin'), setting('youtube')]))]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
<?= setting('head_code') ?>
<script>try{if(sessionStorage.getItem("idl"))document.documentElement.classList.add("no-loader")}catch(e){}</script>
</head>
<body class="<?= e($bodyClass) ?>" data-base="<?= e(base_path()) ?>">
<div class="loader" id="loader" aria-hidden="true"><div class="ld-in"><img src="<?= e(asset(setting('logo') ?: 'assets/img/logo.svg')) ?>" alt="" height="44"><span><?= e($name) ?></span><i class="ld-bar"></i></div></div>
<?php if (setting('topbar_on') && setting('topbar_text')): ?>
<div class="topbar"><div class="container"><span><?= e(setting('topbar_text')) ?></span> <?php if (setting('topbar_link_text')): ?><a href="<?= e(href(setting('topbar_link'))) ?>"><?= e(setting('topbar_link_text')) ?> <i class="bi bi-arrow-right"></i></a><?php endif; ?></div></div>
<?php endif; ?>
<header class="site-header" id="siteHeader">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="brand" href="<?= url('') ?>" aria-label="<?= e($name) ?>"><img src="<?= e(asset(setting('logo') ?: 'assets/img/logo.svg')) ?>" alt="" height="30"><span><?= e($name) ?></span></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu"><i class="bi bi-list"></i></button>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav mx-auto">
          <?php foreach (setting('nav') ?: [] as $n): $h = href($n['link']); $active = ($h === url($page === 'home' ? '' : $page) || ($page === 'home' && $h === url(''))); ?>
          <li class="nav-item"><a class="nav-link<?= $active ? ' active' : '' ?>" href="<?= e($h) ?>"><?= e($n['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
        <div class="nav-actions">
          <?php if (setting('header_link_text') && setting('header_link')): $hl = href(setting('header_link')); $ext = preg_match('#^https?://#', $hl); ?><a class="nav-alt" href="<?= e($hl) ?>"<?= $ext ? ' target="_blank" rel="noopener"' : '' ?>><?= e(setting('header_link_text')) ?><?= $ext ? ' <i class="bi bi-arrow-up-right"></i>' : '' ?></a><?php endif; ?>
          <?php if (setting('header_cta_text')): ?><a class="btn btn-brand btn-sm-pill nav-cta" href="<?= e(href(setting('header_cta_link'))) ?>"><?= e(setting('header_cta_text')) ?> <i class="bi bi-arrow-right"></i></a><?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
</header>
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNav">
  <div class="offcanvas-header"><a class="brand" href="<?= url('') ?>"><img src="<?= e(asset(setting('logo') ?: 'assets/img/logo.svg')) ?>" alt="" height="26"><span><?= e($name) ?></span></a><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
  <div class="offcanvas-body">
    <ul class="list-unstyled mobile-links">
      <?php foreach (setting('nav') ?: [] as $n): ?><li><a href="<?= e(href($n['link'])) ?>"><?= e($n['label']) ?></a></li><?php endforeach; ?>
    </ul>
    <?php if (setting('header_cta_text')): ?><a class="btn btn-brand w-100 mt-3" href="<?= e(href(setting('header_cta_link'))) ?>"><?= e(setting('header_cta_text')) ?></a><?php endif; ?>
  </div>
</div>
<main>
<?php }

function store_buttons($class = '') {
    $a = setting('appstore_url'); $g = setting('playstore_url');
    if (!$a && !$g) return;
    echo '<div class="store-btns ' . e($class) . '">';
    if ($a) echo '<a href="' . e($a) . '" target="_blank" rel="noopener"><i class="bi bi-apple"></i><span><small>Download on the</small>App Store</span></a>';
    if ($g) echo '<a href="' . e($g) . '" target="_blank" rel="noopener"><i class="bi bi-google-play"></i><span><small>Get it on</small>Google Play</span></a>';
    echo '</div>';
}

function page_hero($d) { ?>
<section class="page-hero">
  <div class="container reveal">
    <div class="page-hero-copy">
      <?php if ($d['eyebrow']): ?><span class="eyebrow"><i></i><?= e($d['eyebrow']) ?></span><?php endif; ?>
      <h1><?= rich($d['title']) ?></h1>
      <p class="lead"><?= e($d['text']) ?></p>
    </div>
  </div>
</section>
<?php }

function cta_band($title, $text, $b1t, $b1l, $b2t = '', $b2l = '', $img = '') { ?>
<section class="section pt-0">
  <div class="container">
    <div class="cta-band reveal">
      <div class="row align-items-center g-4">
        <div class="col-lg-<?= $img ? '7' : '12' ?> <?= $img ? '' : 'text-center' ?>">
          <h2><?= rich($title) ?></h2>
          <p><?= e($text) ?></p>
          <div class="d-flex flex-wrap gap-3 <?= $img ? '' : 'justify-content-center' ?>">
            <?php if ($b1t): ?><a class="btn btn-white btn-lg-pill" href="<?= e(href($b1l)) ?>"><?= e($b1t) ?></a><?php endif; ?>
            <?php if ($b2t): ?><a class="btn btn-outline-white btn-lg-pill" href="<?= e(href($b2l)) ?>"><?= e($b2t) ?></a><?php endif; ?>
          </div>
          <?php store_buttons('mt-4'); ?>
        </div>
        <?php if ($img): ?><div class="col-lg-5"><img class="cta-img" src="<?= e(asset($img)) ?>" alt="" loading="lazy"></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php }

function site_foot() {
    $name = setting('site_name'); ?>
</main>
<footer class="site-footer">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-3">
        <?php $fl = trim((string)setting('footer_logo')); $fh = max(24, min(120, (int)setting('footer_logo_height') ?: 56)); ?>
        <a class="brand brand-light foot-logo<?= $fl ? '' : ' foot-logo-badge' ?>" href="<?= url('') ?>" aria-label="<?= e($name ?: 'Home') ?>"><img src="<?= e(asset($fl ?: (setting('logo') ?: 'assets/img/logo.svg'))) ?>" alt="" style="height:<?= $fl ? $fh : min($fh, 40) ?>px"><?php if ($name): ?><span><?= e($name) ?></span><?php endif; ?></a>
        <p class="mt-3 foot-about"><?= e(setting('footer_about')) ?></p>
        <div class="socials">
          <?php foreach (['facebook', 'instagram', 'x_twitter', 'linkedin', 'youtube'] as $s) if (setting($s)): ?><a href="<?= e(setting($s)) ?>" target="_blank" rel="noopener" aria-label="<?= e($s) ?>"><i class="bi <?= icon_link($s) ?>"></i></a><?php endif; ?>
        </div>
        <?php store_buttons('mt-3'); ?>
      </div>
      <?php for ($i = 1; $i <= 3; $i++): $links = setting("footer_links_$i"); if (!$links) continue; ?>
      <div class="col-6 col-lg-2">
        <h6><?= e(setting("footer_links_{$i}_title")) ?></h6>
        <ul class="list-unstyled">
          <?php foreach ($links as $l): ?><li><a href="<?= e(href($l['link'])) ?>"><?= e($l['label']) ?></a></li><?php endforeach; ?>
        </ul>
      </div>
      <?php endfor; ?>
      <div class="col-12 col-lg-3">
        <h6>Contact</h6>
        <ul class="list-unstyled foot-contact">
          <?php contact_rows('footer'); ?>
        </ul>
      </div>
    </div>
    <div class="foot-mega" aria-hidden="true"><?= e($name) ?></div>
    <div class="foot-bottom"><span><?= e(str_replace('{year}', date('Y'), setting('copyright'))) ?></span></div>
  </div>
</footer>
<script src="<?= asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('assets/js/site.js') ?>?v=1.0"></script>
<script src="<?= asset('assets/js/premium.js') ?>?v=2.0"></script>
<?= setting('body_code') ?>
</body>
</html>
<?php }
