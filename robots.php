<?php
require_once __DIR__ . '/includes/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /data/\nDisallow: /includes/\n";
foreach (lines(setting('robots_extra')) as $l) echo $l . "\n";
echo "\nSitemap: " . site_origin() . "/sitemap.xml\n";
