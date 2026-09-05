<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();

if ($thisstaff && $thisstaff->is2FAPending())
    $msg = "2FA Pending";

$company = Format::htmlchars($ost->company) ?: 'arahOS Help Desk';
$checked = date('M j, Y · g:ia');
?>
<div id="arahOS-login-wrap">

  <!-- ================= LEFT: credentials ================= -->
  <div id="arahOS-login-left">
    <div id="arahOS-login-card">

      <div class="vl-brandline vl-brandline-logo">
        <img class="vl-banner-logo" src="<?php echo ROOT_PATH; ?>images/arahOS/arahOS-banner-logo.png" alt="<?php echo $company; ?>">
      </div>

      <h2>arahOS Help Desk</h2>
      <p class="vl-sub">Enter your arahOS network credentials</p>

      <?php $is_err = ($_POST && $errors) || $show_reset; ?>
      <h3 id="login-message" class="vl-msg <?php echo $msg ? 'show' : ''; ?> <?php echo $is_err ? 'error' : ''; ?>"><?php echo Format::htmlchars($msg); ?></h3>
      <?php if ($content) { ?>
        <div class="banner" style="font-size:.8rem;color:#6b7280;margin-bottom:12px;"><?php echo Format::display($content->getLocalBody()); ?></div>
      <?php } ?>

      <div id="loading" style="display:none;" class="dialog">
        <h1><i class="icon-spinner icon-spin icon-large"></i> <?php echo __('Verifying');?></h1>
      </div>

      <form action="login.php" method="post" id="login" onsubmit="attemptLoginAjax(event)">
        <?php csrf_token();
        if ($thisstaff
                &&  $thisstaff->is2FAPending()
                && ($bk=$thisstaff->get2FABackend())
                && ($form=$bk->getInputForm($_POST))) {
            include STAFFINC_DIR . 'templates/dynamic-form-simple.tmpl.php';
            ?>
            <fieldset style="padding-top:10px;">
            <input type="hidden" name="do" value="2fa">
            <button class="vl-signin" type="submit" name="submit"><?php echo __('Verify'); ?></button>
            </fieldset>
        <?php
        } else { ?>
            <input type="hidden" name="do" value="scplogin">
            <div class="vl-field">
              <i class="fa fa-user"></i>
              <input type="text" name="userid" id="name" value="<?php echo $info['userid'] ?? null; ?>"
                     placeholder="firstname.lastname" autofocus autocorrect="off" autocapitalize="off">
            </div>
            <div class="vl-field">
              <i class="fa fa-lock"></i>
              <input type="password" name="passwd" id="pass" maxlength="128"
                     placeholder="<?php echo __('Password'); ?>" autocorrect="off" autocapitalize="off">
            </div>
            <p class="vl-hint">
              Sign in with your username (e.g. <strong>firstname.lastname</strong>).<br>
              You may also use <strong>firstname.lastname@arahOS.vi</strong> or <strong>firstname.lastname@k12.vi</strong>.
            </p>
            <button class="vl-signin" type="submit" name="submit"><?php echo __('Sign in'); ?></button>

            <div class="vl-links">
              <div class="row">
                <a id="reset-link" class="vl-reset <?php if (!$show_reset || !$cfg->allowPasswordReset()) echo 'hidden'; ?>"
                   href="pwreset.php"><?php echo __('Forgot My Password'); ?></a>
              </div>
            </div>
        <?php
        } ?>
      </form>

      <?php if (($bks=StaffAuthenticationBackend::getExternal())) { ?>
        <div class="or"><hr/></div>
        <?php foreach ($bks as $bk) { ?>
          <div class="external-auth"><?php $bk->renderExternalLink(); ?></div><br/>
        <?php } ?>
      <?php } ?>

      <div class="vl-foot" id="company">
        <div class="content"><?php echo __('Copyright'); ?> &copy; <?php echo $company; ?></div>
      </div>

    </div>
  </div>

  <!-- ================= RIGHT: status dashboard ================= -->
  <div id="arahOS-login-right">
    <div class="vl-panel">
      <h3>Support services</h3>
      <p class="vl-panelsub">arahOS help desk status &amp; contact channels.</p>

      <span class="vl-allok"><span class="dot"></span> All systems operational</span>

      <div class="vl-grid">
        <div class="vl-svc">
          <div class="h"><i class="fa fa-check-circle i-green"></i> Help desk</div>
          <div class="st">Operational</div>
          <div class="d">Ticketing system online</div>
        </div>
        <div class="vl-svc">
          <div class="h"><i class="fa fa-lock i-gold"></i> LDAP / AD</div>
          <div class="st">Operational</div>
          <div class="d">Directory reachable over LDAPS</div>
        </div>
        <div class="vl-svc">
          <div class="h"><i class="fa fa-envelope i-green"></i> Email delivery</div>
          <div class="st">Operational</div>
          <div class="d">SMTP relay 192.168.1.12</div>
        </div>
        <div class="vl-svc">
          <div class="h"><i class="fa fa-phone i-blue"></i> Phone support</div>
          <div class="st" style="color:#6db3ff;">Available</div>
          <div class="d">STX 340-773-1095 · STTJ 774-0100</div>
        </div>
      </div>

      <div class="vl-tiles">
        <div class="vl-tile"><b>24/7</b> Monitoring</div>
        <div class="vl-tile"><b>SLA</b> Tracked</div>
        <div class="vl-tile"><b>ARA</b> Help Desk</div>
      </div>

      <div class="vl-checked">Last checked: <?php echo $checked; ?></div>
    </div>
  </div>

</div>

<div id="poweredBy"><?php echo __('Powered by'); ?>
  <a href="http://www.osticket.com" target="_blank">
    <img alt="osTicket" src="images/osticket-grey.png" class="osticket-logo">
  </a>
</div>

<script>
function attemptLoginAjax(e) {
    $('#loading').show();
    var objectifyForm = function(formArray) {
        var returnArray = {};
        for (var i = 0; i < formArray.length; i++) {
            returnArray[formArray[i]['name']] = formArray[i]['value'];
        }
        return returnArray;
    };
    var form = $(e.target),
        data = objectifyForm(form.serializeArray());
    data.ajax = 1;
    $('button[type=submit]', form).attr('disabled', 'disabled');
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: data,
        cache: false,
        success: function(json) {
            $('button[type=submit]', form).removeAttr('disabled');
            if (!typeof(json) === 'object' || !json.status)
                return;
            switch (json.status) {
            case 401:
                if (json && json.redirect)
                    document.location.href = json.redirect;
                if (json && json.message) {
                    $('#login-message').text(json.message).addClass('show error');
                }
                if (json && json.show_reset)
                    $('#reset-link').removeClass('hidden').show();
                $('#loading').hide();
                $('#pass').val('').focus();
                break;
            case 302:
                if (json && json.redirect)
                    document.location.href = json.redirect;
                break;
            }
        },
    });
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}
</script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js?8d38b06"></script>
</body>
</html>
