<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/blocks.php';
$P = 'contact'; $h = page_content($P);
site_head($P, 'page-inner');
page_hero($h['hero']);
$f = $h['form']; ?>
<section class="section pt-0">
  <div class="container">
    <div class="row g-4 g-lg-5">
      <div class="col-lg-7 reveal">
        <div class="form-card">
          <h2 class="h4 mb-4"><?= e($f['title']) ?></h2>
          <form id="contactForm" method="post" action="<?= e(asset('contact-submit.php')) ?>" novalidate>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Full name</label><input class="form-control" name="name" required maxlength="120" placeholder="Your name"></div>
              <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required maxlength="160" placeholder="you@example.com"></div>
              <div class="col-md-6"><label class="form-label">Phone <span class="text-muted">(optional)</span></label><input class="form-control" name="phone" maxlength="40" placeholder="+1 307 000 0000"></div>
              <div class="col-md-6"><label class="form-label">Topic</label><select class="form-select" name="topic"><?php foreach (lines($f['topics']) as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></div>
              <div class="col-12"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="5" required maxlength="4000" placeholder="How can we help?"></textarea></div>
              <div class="d-none"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
              <input type="hidden" name="ts" value="<?= time() ?>">
              <div class="col-12"><button class="btn btn-brand btn-lg-pill w-100" type="submit"><span class="lbl"><?= e($f['button']) ?></span></button></div>
              <div class="col-12"><div id="formMsg" class="form-msg" data-success="<?= e($f['success']) ?>" role="status"></div></div>
            </div>
          </form>
        </div>
      </div>
      <div class="col-lg-5 reveal">
        <div class="side-card">
          <h3><?= e($f['side_title']) ?></h3>
          <p><?= e($f['side_text']) ?></p>
          <ul class="list-unstyled contact-list">
            <?php contact_rows('contact'); ?>
                        <?php if (setting('hours')): ?><li><i class="bi bi-clock"></i><div><small>Support hours</small><?= e(setting('hours')) ?></div></li><?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
<?php site_foot();
