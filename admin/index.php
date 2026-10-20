<?php
require_once __DIR__ . '/_boot.php';
require_login();
$leads = store_read('leads', []); $smtp = smtp_cfg();
$unread = unread_leads(); $media = count(glob(UPLOAD_DIR . '/*.{jpg,jpeg,png,webp,gif,svg}', GLOB_BRACE));
$writable = is_writable(DATA_DIR) && is_writable(UPLOAD_DIR);
$checks = [
    ['Data folder writable', $writable, 'Set permissions of /data and /uploads to 755 (or 775).'],
    ['SMTP email configured', !empty($smtp['smtp_on']) && $smtp['smtp_host'], 'Set it up in SMTP / Email so contact messages reach your inbox.'],
    ['Website URL set', setting('site_url') !== '', 'Set in Site Settings → General.'],
    ['Social share image set', (bool)setting('default_og'), 'Site Settings → Global SEO.'],
    ['Search Console verified', (bool)setting('google_verification'), 'Optional: add your verification code in Global SEO.'],
    ['HTTPS enabled', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'Enable the free SSL certificate in your hosting panel.'],
];
admin_head('Dashboard', 'index.php'); ?>
<div class="row g-3 mb-4">
  <?php foreach ([['Pages', count(pages_schema()), 'bi-file-earmark-richtext', 'content.php'], ['New messages', $unread, 'bi-inbox', 'leads.php'], ['Total messages', count($leads), 'bi-chat-left-text', 'leads.php'], ['Images', $media, 'bi-images', 'media.php']] as [$l, $v, $i, $h]): ?>
  <div class="col-6 col-xl-3"><a class="kpi" href="<?= $h ?>"><i class="bi <?= $i ?>"></i><div><div class="kpi-v"><?= $v ?></div><div class="kpi-l"><?= $l ?></div></div></a></div>
  <?php endforeach; ?>
</div>
<div class="row g-4">
  <div class="col-xl-8">
    <div class="card-box mb-4"><h5 class="mb-3">Quick actions</h5>
      <div class="row g-3">
        <?php foreach ([['Edit homepage', 'content.php?page=home', 'bi-pencil-square'], ['Edit SEO tags', 'seo.php', 'bi-search'], ['Upload images', 'media.php', 'bi-cloud-upload'], ['Email / SMTP', 'smtp.php', 'bi-envelope-at'], ['Menu & footer', 'settings.php#navigation', 'bi-list'], ['Logo & colours', 'settings.php#branding', 'bi-palette']] as [$l, $h, $i]): ?>
        <div class="col-6 col-md-4"><a class="qa" href="<?= $h ?>"><i class="bi <?= $i ?>"></i><?= $l ?></a></div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="card-box"><div class="d-flex mb-3"><h5 class="mb-0">Latest messages</h5><a class="ms-auto small" href="leads.php">View all</a></div>
      <?php if (!$leads): ?><p class="text-muted mb-0">No messages yet.</p><?php endif; ?>
      <?php foreach (array_slice($leads, 0, 5) as $l): ?><div class="lead-mini <?= $l['read'] ? '' : 'unread' ?>"><div><strong><?= e($l['name']) ?></strong> <span class="text-muted">· <?= e($l['email']) ?></span><div class="text-muted small text-truncate"><?= e(mb_substr($l['message'], 0, 90)) ?></div></div><small class="text-muted"><?= e(date('d M', strtotime($l['date']))) ?></small></div><?php endforeach; ?>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card-box"><h5 class="mb-3">Setup checklist</h5>
      <?php foreach ($checks as [$l, $ok, $tip]): ?><div class="check <?= $ok ? 'ok' : '' ?>"><i class="bi <?= $ok ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i><div><div><?= e($l) ?></div><?php if (!$ok): ?><small class="text-muted"><?= e($tip) ?></small><?php endif; ?></div></div><?php endforeach; ?>
    </div>
  </div>
</div>
<?php admin_foot();
