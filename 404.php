<?php
require_once __DIR__ . '/includes/layout.php';
http_response_code(404);
$GLOBALS['__404'] = true;
site_head('home', 'page-inner');
?>
<section class="page-hero"><div class="container text-center"><span class="eyebrow"><i></i>Error 404</span><h1>Page not <span class="hl">found</span></h1><p class="lead">The page you’re looking for doesn’t exist or has moved.</p><a class="btn btn-brand btn-lg-pill" href="<?= url('') ?>">Back to home</a></div></section>
<?php site_foot();
