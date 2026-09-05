<?php
$title=($cfg && is_object($cfg) && $cfg->getTitle())
    ? $cfg->getTitle() : 'osTicket :: '.__('Support Ticket System');
$signin_url = ROOT_PATH . "login.php"
    . ($thisclient ? "?e=".urlencode($thisclient->getEmail()) : "");
$signout_url = ROOT_PATH . "logout.php?auth=".$ost->getLinkToken();

header("Content-Type: text/html; charset=UTF-8");
header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes()."; script-src 'self' 'unsafe-inline'; object-src 'none'");

if (($lang = Internationalization::getCurrentLanguage())) {
    $langs = array_unique(array($lang, $cfg->getPrimaryLanguage()));
    $langs = Internationalization::rfc1766($langs);
    header("Content-Language: ".implode(', ', $langs));
}
?>
<!DOCTYPE html>
<html<?php
if ($lang
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . $lang . '"';
}

// Dropped IE Support Warning
if (osTicket::is_ie())
    $ost->setWarning(__('osTicket no longer supports Internet Explorer.'));
?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo Format::htmlchars($title); ?></title>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#0a1f44">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="arahOS Help Desk">
    <link rel="apple-touch-icon" href="/images/arahOS/pwa/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/images/arahOS/pwa/icon-192.png">
	<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/osticket.css?8d38b06" media="screen"/>
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/theme.css?8d38b06" media="screen"/>
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/print.css?8d38b06" media="print"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/typeahead.css?8d38b06"
         media="screen" />
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.13.2.custom.min.css?8d38b06"
        rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/jquery-ui-timepicker-addon.css?8d38b06" media="all"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/thread.css?8d38b06" media="screen"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?8d38b06" media="screen"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/arahOS/arahOS-tokens.css?v=3">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/arahOS/arahOS-client-portal.css?v=34">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?8d38b06"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css?8d38b06"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?8d38b06"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css?8d38b06"/>
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/oscar-favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/oscar-favicon-16x16.png" sizes="16x16" />
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js?8d38b06"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js?8d38b06"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-timepicker-addon.js?8d38b06"></script>
    <script src="<?php echo ROOT_PATH; ?>js/osticket.js?8d38b06"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js?8d38b06"></script>
    <script src="<?php echo ROOT_PATH; ?>js/bootstrap-typeahead.js?8d38b06"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js?8d38b06"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-plugins.js?8d38b06"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js?8d38b06"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js?8d38b06"></script>
    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }

    // Offer alternate links for search engines
    // @see https://support.google.com/webmasters/answer/189077?hl=en
    if (($all_langs = Internationalization::getConfiguredSystemLanguages())
        && (count($all_langs) > 1)
    ) {
        $langs = Internationalization::rfc1766(array_keys($all_langs));
        $qs = array();
        parse_str($_SERVER['QUERY_STRING'], $qs);
        foreach ($langs as $L) {
            $qs['lang'] = $L; ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>?<?php
            echo http_build_query($qs); ?>" hreflang="<?php echo $L; ?>" />
<?php
        } ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>"
            hreflang="x-default" />
<?php
    }
    ?>
</head>
<body<?php if (basename($_SERVER['SCRIPT_NAME']) === 'login.php' || basename($_SERVER['SCRIPT_NAME']) === 'pwreset.php') echo ' class="auth-page"'; ?>>
    <div id="container">
        <?php
        if($ost->getError())
            echo sprintf('<div class="error_bar">%s</div>', $ost->getError());
        elseif($ost->getWarning())
            echo sprintf('<div class="warning_bar">%s</div>', $ost->getWarning());
        elseif($ost->getNotice())
            echo sprintf('<div class="notice_bar">%s</div>', $ost->getNotice());
        ?>
        <div id="header">
            <a class="arahOS-header-logo" id="logo" href="<?php echo ROOT_PATH; ?>index.php"
            title="<?php echo __('Support Center'); ?>">
                <img src="<?php echo ROOT_PATH; ?>images/arahOS/arahOS-banner-logo.png" border=0 alt="<?php
                echo $ost->getConfig()->getTitle(); ?>">
            </a>
            <div class="arahOS-header-right">
                <?php if($nav){ ?>
                <ul id="nav" class="arahOS-header-nav">
                    <?php
                    if($nav && ($navs=$nav->getNavLinks()) && is_array($navs)){
                        foreach($navs as $name =>$nav) {
                            echo sprintf('<li><a class="%s %s" href="%s">%s</a></li>%s',$nav['active']?'active':'',$name,(ROOT_PATH.$nav['href']),$nav['desc'],"\n");
                        }
                    } ?>
                </ul>
                <?php } ?>
                <div class="arahOS-header-user">
                 <?php
                    if ($thisclient && is_object($thisclient) && $thisclient->isValid()
                        && !$thisclient->isGuest()) {
                     echo Format::htmlchars($thisclient->getName()).'&nbsp;|';
                     ?>
                    <a href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a> |
                    <a href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets <b>(%d)</b>'), $thisclient->getNumTickets()); ?></a> -
                    <a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a>
                <?php
                } elseif($nav) {
                    if ($cfg->getClientRegistrationMode() == 'public') { ?>
                        <span class="arahOS-guest"><?php echo __('Guest User'); ?></span> <span class="arahOS-sep">|</span> <?php
                    }
                    if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
                        <a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a><?php
                    }
                    elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
                        <a href="<?php echo $signin_url; ?>"><?php echo __('Sign In'); ?></a>
    <?php
                    }
                } ?>
                <?php
                if (($all_langs = Internationalization::getConfiguredSystemLanguages())
                    && (count($all_langs) > 1)
                ) {
                    $qs = array();
                    parse_str($_SERVER['QUERY_STRING'], $qs);
                    foreach ($all_langs as $code=>$info) {
                        list($lang, $locale) = explode('_', $code);
                        $qs['lang'] = $code;
                ?>
                        <a class="flag flag-<?php echo strtolower($info['flag'] ?: $locale ?: $lang); ?>"
                            href="?<?php echo http_build_query($qs);
                            ?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
                <?php }
                } ?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div id="content">

         <?php if($errors['err']) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
         <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
         <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
         <?php } ?>
