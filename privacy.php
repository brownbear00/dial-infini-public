<?php
require_once __DIR__ . '/includes/layout.php';
$P = 'privacy'; $h = page_content($P); $d = $h['content'];
site_head($P, 'page-inner');
?>
<section class="page-hero compact"><div class="container text-center"><h1><?= e($d['title']) ?></h1><p class="lead">Last updated: <?= e($d['updated']) ?></p></div></section>
<section class="section pt-0"><div class="container"><article class="legal reveal"><?= $d['body'] ?></article></div></section>
<?php site_foot();
