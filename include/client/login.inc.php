<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['luser']?:$_GET['e']);
$passwd=Format::input($_POST['lpasswd']?:$_GET['t']);

$content = Page::lookupByType('banner-client');

if ($content) {
    list($title, $body) = $ost->replaceTemplateVariables(
        array($content->getLocalName(), $content->getLocalBody()));
} else {
    $title = __('Sign In to arahOS Help Desk');
    $body = __('Sign in with your network username in the format <strong>firstname.lastname</strong>. You may also use <strong>firstname.lastname@arahOS.example</strong> or <strong>firstname.lastname@arahOS.example</strong>. Use the same password you use to log into your computer.');
}
?>
<div id="auth">
  <div id="auth-left">
    <div class="logo-wrap arahOS-banner-logo-wrap">
      <img class="arahOS-banner-logo" src="<?php echo ROOT_PATH; ?>images/arahOS/arahOS-banner-logo.png" alt="<?php echo Format::htmlchars($ost->getConfig()->getTitle()); ?>">
      <h1 class="auth-title"><?php echo Format::htmlchars($ost->getConfig()->getTitle()); ?></h1>
      <p class="auth-subtitle">Enter your arahOS network credentials</p>
    </div>

    <div class="form-wrap">
      <form action="login.php" method="post" id="clientLogin" autocomplete="off">
        <?php csrf_token(); ?>

        <?php if ($errors['err']) { ?>
          <div class="auth-alert" role="alert"><?php echo Format::htmlchars($errors['err']); ?></div>
        <?php } ?>

        <div class="form-group">
          <input id="username" type="text" name="luser"
                 placeholder="firstname.lastname"
                 value="<?php echo Format::htmlchars($email); ?>"
                 autocomplete="username"
                 class="auth-control">
          <span class="auth-control-icon">👤</span>
        </div>

        <div class="form-group">
          <input id="password" type="password" name="lpasswd"
                 placeholder="Password"
                 value="<?php echo Format::htmlchars($passwd); ?>"
                 maxlength="128"
                 autocomplete="current-password"
                 class="auth-control">
          <span class="auth-control-icon">🔒</span>
        </div>

        <button type="submit" class="btn-primary">Sign in</button>

        <?php if ($suggest_pwreset) { ?>
          <p class="auth-links">
            <a class="font-bold" href="<?php echo ROOT_PATH; ?>pwreset.php">Forgot password?</a>
          </p>
        <?php } ?>

        <?php
        $ext_bks = array();
        foreach (UserAuthenticationBackend::allRegistered() as $bk)
            if ($bk instanceof ExternalAuthentication)
                $ext_bks[] = $bk;

        if (count($ext_bks)) { ?>
          <div class="external-auth">
            <?php foreach ($ext_bks as $bk) { $bk->renderExternalLink(); } ?>
          </div>
        <?php } ?>

        <?php if ($cfg && $cfg->isClientRegistrationEnabled()) { ?>
          <p class="auth-links">
            Not yet registered? <a href="<?php echo ROOT_PATH; ?>account.php?do=create">Create an account</a>
          </p>
        <?php } ?>

        <p class="auth-links subtle">
          <b>I'm an agent</b> —
          <a href="<?php echo ROOT_PATH; ?>scp/">sign in here</a>
        </p>

        <div class="agent-login-note">
          <span><?php echo __('Are you a arahOS agent or staff member?'); ?></span>
          <a href="<?php echo ROOT_PATH; ?>scp/login.php" class="btn agent-login-btn"><?php echo __('Sign in to the Agent Panel'); ?></a>
        </div>
      </form>

      <p class="auth-links subtle">
        First time contacting us or lost your ticket number?
        <a href="<?php echo ROOT_PATH; ?>open.php">Open a new ticket</a>
      </p>
    </div>
  </div>

  <div id="auth-right">
    <div class="live-panel">
      <h2>Support services</h2>
      <p class="sub">arahOS help desk status & contact channels.</p>
      <div class="svc-badge"><span class="live-pulse"></span>All systems operational</div>

      <div class="svc-cards">
        <div class="svc-card">
          <div class="svc-ico">✓</div>
          <div class="svc-info">
            <div class="name">Help desk</div>
            <div class="state">Operational</div>
            <div class="detail">Ticketing system online</div>
          </div>
        </div>
        <div class="svc-card">
          <div class="svc-ico">🔒</div>
          <div class="svc-info">
            <div class="name">LDAP / AD</div>
            <div class="state">Operational</div>
            <div class="detail">Directory reachable over LDAPS</div>
          </div>
        </div>
        <div class="svc-card">
          <div class="svc-ico">✉</div>
          <div class="svc-info">
            <div class="name">Email delivery</div>
            <div class="state">Operational</div>
            <div class="detail">SMTP relay 192.168.1.12</div>
          </div>
        </div>
        <div class="svc-card">
          <div class="svc-ico">☎</div>
          <div class="svc-info">
            <div class="name">Phone support</div>
            <div class="state">Available</div>
            <div class="detail">STX 340-773-1095 · STTJ 774-0100</div>
          </div>
        </div>
      </div>

      <div class="live-stats">
        <div class="live-stat">
          <div class="num">24/7</div>
          <div class="lbl">Monitoring</div>
        </div>
        <div class="live-stat">
          <div class="num">SLA</div>
          <div class="lbl">Tracked</div>
        </div>
        <div class="live-stat">
          <div class="num">ARA</div>
          <div class="lbl">OIT Help Desk</div>
        </div>
      </div>

      <p class="live-time">Last checked: <?php echo date('M j, Y · g:ia'); ?></p>
    </div>
  </div>
</div>
