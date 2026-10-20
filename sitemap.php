<?php
require_once __DIR__ . '/includes/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');
$o = site_origin(); $saved = store_read('seo');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach (pages_schema() as $k => $p) {
    $seo = seo_for($k);
    if (stripos($seo['robots'], 'noindex') !== false) continue;
    $path = $k === 'home' ? '/' : '/' . $p['slug'] . (setting('clean_urls') ? '' : '.php');
    $f = ROOT . '/' . ($k === 'home' ? 'index' : $p['slug']) . '.php';
    echo "  <url><loc>" . e($o . $path) . "</loc><lastmod>" . date('Y-m-d', max((int)@filemtime(DATA_DIR . "/content.json.php"), (int)filemtime($f))) . "</lastmod><priority>" . ($k === 'home' ? '1.0' : '0.8') . "</priority></url>\n";
}
echo "</urlset>\n";
