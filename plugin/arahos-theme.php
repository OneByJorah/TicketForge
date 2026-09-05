<?php
/**
 * arahOST Help Desk Theme for osTicket
 *
 * Brands the osTicket client support portal and the staff (agent) control
 * panel with the arahOS navy (#0a1f44) + gold (#f2a900) design.
 *
 * HOW IT WORKS
 * -----------
 * osTicket bootstraps plugins during Osticket::start(), which runs BEFORE
 * the global $ost (and thus addExtraHeader()) is available. To avoid that
 * timing trap, the actual CSS is injected by the overridden header templates
 * (include/client/header.inc.php and include/staff/header.inc.php), which
 * render after $ost/$cfg are live. This plugin still exists so the theme is
 * discoverable/enabled in Admin Panel -> Manage -> Plugins and so the
 * getInstallPath() (plugin root) is known; the templates read the CSS from
 * the plugin folder on disk (INCLUDE_DIR is not web-served, so an inline
 * <style> is emitted rather than a <link>).
 */

class arahOSOSTicketTheme extends Plugin {

    // Declared so the plugin instance can be configured/enabled in the UI.
    var $config_class = 'arahOSOSTicketThemeConfig';

    /**
     * Required by osTicket — PluginInstance::bootstrap() calls this
     * unconditionally during Osticket::start(). At that point the global
     * $ost (and addExtraHeader) is NOT yet available, so we intentionally
     * do nothing here. The actual navy/gold CSS is injected by the
     * overridden header templates (client/header.inc.php and
     * staff/header.inc.php) which render after $ost/$cfg are live and
     * call arahOSOSTicketTheme::cssBlock().
     */
    function bootstrap() {
        // Intentionally empty — see cssBlock() and the header templates.
    }

    /**
     * Public helper so the header templates can locate and read the CSS.
     * Returns the inline <style> block (empty string if unavailable).
     */
    static function cssBlock($which) {
        $file = $which === 'staff' ? 'staff.css' : 'client.css';
        $base = INCLUDE_DIR . 'plugins/arahOS-theme/';
        $path = $base . $file;
        if (!is_file($path) || !is_readable($path)) {
            // Fall back to repo-relative layout.
            $alt = dirname(__FILE__) . '/' . $file;
            $path = is_file($alt) ? $alt : $path;
        }
        if (!is_file($path) || !is_readable($path)) {
            return '';
        }
        $css = file_get_contents($path);
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = trim($css);
        if ($css === '') {
            return '';
        }
        return sprintf(
            "<style id=\"arahOS-theme\" type=\"text/css\">\n%s\n</style>",
            $css
        );
    }
}

class arahOSOSTicketThemeConfig extends PluginConfig {
}
