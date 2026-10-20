<?php
require_once __DIR__ . '/_boot.php';
require_login();
$leads = store_read('leads', []);
if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="messages-' . date('Y-m-d') . '.csv"');
    $o = fopen('php://output', 'w'); fputcsv($o, ['Date', 'Name', 'Email', 'Phone', 'Topic', 'Message', 'Email delivery']);
    foreach ($leads as $l) fputcsv($o, [$l['date'], $l['name'], $l['email'], $l['phone'], $l['topic'], $l['message'], $l['mail']]);
    exit;
}
if (is_post()) {
    csrf_check(); $id = $_POST['id'] ?? ''; $act = $_POST['action'] ?? '';
    foreach ($leads as $i => &$l) {
        if ($l['id'] !== $id && $act !== 'readall') continue;
        if ($act === 'read' || $act === 'readall') $l['read'] = 1;
        if ($act === 'unread') $l['read'] = 0;
        if ($act === 'delete') { unset($leads[$i]); }
    }
    unset($l); store_write('leads', array_values($leads)); redirect(aurl('leads.php'));
}
admin_head('Messages', 'leads.php'); ?>
<div class="d-flex flex-wrap gap-2 mb-3">
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="readall"><button class="btn btn-light"><i class="bi bi-check2-all"></i> Mark all read</button></form>
  <a class="btn btn-light" href="?export=csv"><i class="bi bi-download"></i> Export CSV</a>
  <span class="ms-auto text-muted align-self-center"><?= count($leads) ?> message<?= count($leads) === 1 ? '' : 's' ?></span>
</div>
<?php if (!$leads): ?><div class="card-box text-center text-muted py-5"><i class="bi bi-inbox display-5"></i><p class="mt-2 mb-0">No messages yet. Contact-form submissions will appear here.</p></div><?php endif; ?>
<div class="accordion lead-acc" id="leadAcc">
<?php foreach ($leads as $i => $l): ?>
  <div class="accordion-item <?= $l['read'] ? '' : 'unread' ?>">
    <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#l<?= $i ?>">
      <span class="dot"></span><strong class="me-2"><?= e($l['name']) ?></strong><span class="text-muted me-auto text-truncate"><?= e($l['topic']) ?> — <?= e(mb_substr($l['message'], 0, 70)) ?></span><small class="text-muted ms-3 flex-none"><?= e(date('d M Y, H:i', strtotime($l['date']))) ?></small></button></h2>
    <div id="l<?= $i ?>" class="accordion-collapse collapse"><div class="accordion-body">
      <div class="row g-3 mb-3"><div class="col-md-4"><small class="text-muted d-block">Email</small><a href="mailto:<?= e($l['email']) ?>"><?= e($l['email']) ?></a></div><div class="col-md-4"><small class="text-muted d-block">Phone</small><?= e($l['phone'] ?: '—') ?></div><div class="col-md-4"><small class="text-muted d-block">Email notification</small><?= $l['mail'] === 'sent' ? '<span class="text-success">Delivered to inbox</span>' : '<span class="text-danger">' . e($l['mail'] ?: 'n/a') . '</span>' ?></div></div>
      <div class="msg-body"><?= nl($l['message']) ?></div>
      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-brand btn-sm" href="mailto:<?= e($l['email']) ?>?subject=Re: <?= e(rawurlencode($l['topic'])) ?>"><i class="bi bi-reply"></i> Reply</a>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($l['id']) ?>"><input type="hidden" name="action" value="<?= $l['read'] ? 'unread' : 'read' ?>"><button class="btn btn-light btn-sm"><?= $l['read'] ? 'Mark unread' : 'Mark read' ?></button></form>
        <form method="post" onsubmit="return confirm('Delete this message?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($l['id']) ?>"><input type="hidden" name="action" value="delete"><button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
      </div>
    </div></div>
  </div>
<?php endforeach; ?>
</div>
<?php admin_foot();
