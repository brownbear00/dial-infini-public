<?php
require_once __DIR__ . '/_boot.php';
require_login();
$fields = smtp_fields();
if (is_post()) {
    csrf_check();
    if (($_POST['action'] ?? '') === 'test') {
        $to = trim($_POST['to'] ?? '');
        [$ok, $info] = mail_send($to, 'SMTP test from ' . setting('site_name'), "This is a test email sent from your website dashboard.\n\nIf you can read this, your email settings work.\n\nSent: " . date('c'));
        json_out(['ok' => $ok, 'info' => $info]);
    }
    $old = smtp_cfg(); $new = collect_fields($fields, $_POST['f'] ?? []);
    if ($new['smtp_pass'] === '') $new['smtp_pass'] = $old['smtp_pass'] ?? '';
    store_write('smtp', $new);
    flash('Email settings saved. Use “Send test email” to verify them.'); redirect(aurl('smtp.php'));
}
$cfg = smtp_cfg(); $cfg['smtp_pass'] = $cfg['smtp_pass'] ? 'x' : '';
admin_head('SMTP / Email', 'smtp.php'); ?>
<div class="row g-4">
  <div class="col-lg-7">
    <form method="post" class="card-box"><?= csrf_field() ?>
      <h5 class="mb-3">SMTP server</h5>
      <?php
      $cols = ['smtp_on' => 12, 'smtp_host' => 8, 'smtp_port' => 4, 'smtp_secure' => 6, 'smtp_user' => 6, 'smtp_pass' => 6, 'smtp_from' => 6, 'smtp_from_name' => 6];
      echo '<div class="row">'; foreach ($fields as $k => $f) echo '<div class="col-md-' . $cols[$k] . '">' . render_field("f[$k]", $f, $cfg[$k]) . '</div>'; echo '</div>'; ?>
      <button class="btn btn-brand"><i class="bi bi-check2"></i> Save email settings</button>
    </form>
  </div>
  <div class="col-lg-5">
    <div class="card-box mb-4"><h5>Send a test email</h5>
      <p class="text-muted small">Save your settings first, then send a test to check the connection.</p>
      <div class="input-group mb-3"><input type="email" class="form-control" id="testTo" placeholder="you@example.com" value="<?= e(trim(explode(',', setting('notify_email'))[0])) ?>"><button class="btn btn-brand" id="testBtn">Send test</button></div>
      <div id="testOut"></div>
    </div>
    <div class="card-box"><h5>Typical settings</h5>
      <ul class="small text-muted mb-0 ps-3">
        <li><strong>cPanel hosting:</strong> host <code>mail.dialinfini.com</code>, port <code>465</code>, SSL, username = full mailbox (e.g. <code>no-reply@dialinfini.com</code>).</li>
        <li><strong>Gmail:</strong> <code>smtp.gmail.com</code>, 465 SSL or 587 STARTTLS, with an App Password.</li>
        <li>“From email” should be the same mailbox you log in with, otherwise many servers reject or spam-flag the mail.</li>
        <li>Where do messages go? <a href="settings.php#forms">Site Settings → Contact Form</a>.</li>
      </ul>
    </div>
  </div>
</div>
<script>
document.getElementById('testBtn').addEventListener('click',function(){
  var b=this,o=document.getElementById('testOut');b.disabled=true;o.innerHTML='<span class="text-muted">Connecting…</span>';
  var fd=new FormData();fd.append('action','test');fd.append('_csrf',document.body.dataset.csrf);fd.append('to',document.getElementById('testTo').value);
  fetch('smtp.php',{method:'POST',body:fd}).then(r=>r.json()).then(j=>{o.innerHTML='<div class="alert alert-'+(j.ok?'success':'danger')+' mb-0">'+(j.ok?'✓ ':'✕ ')+j.info.replace(/</g,'&lt;')+'</div>'}).catch(()=>{o.innerHTML='<div class="alert alert-danger mb-0">Request failed</div>'}).finally(()=>b.disabled=false);
});
</script>
<?php admin_foot();
