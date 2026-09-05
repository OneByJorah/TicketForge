# Staff Panel — Injection Point

File to edit: `include/staff/header.inc.php`

1. **Back this up too — this is the panel your agents live in all day:**
   ```bash
   cp include/staff/header.inc.php include/staff/header.inc.php.bak
   ```

2. Find where `css/scp.css` (or similar) is loaded inside `<head>`.

3. Immediately after it, add:
   ```php
   <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/helpdesk/helpdesk-tokens.css">
   <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/helpdesk/helpdesk-staff-panel.css">
   ```

3b. Right before `</body>` in the same file (or in the staff footer include
   if there is one), add:
   ```php
   <script src="<?php echo ROOT_PATH; ?>js/helpdesk/helpdesk-staff-panel.js"></script>
   ```
   This powers the mobile sidebar toggle and the dark-mode toggle — CSS
   alone won't add those, they need the JS.

4. Upload:
   - `css/helpdesk/helpdesk-tokens.css`
   - `css/helpdesk/helpdesk-staff-panel.css`
   - `js/helpdesk/helpdesk-staff-panel.js`
   - your logo to `images/helpdesk/helpdesk-logo.png`

5. **Test on a staging copy of osTicket first, or at minimum during
   off-hours.** The staff panel is JS-heavy (drag-resizable ticket
   panes, AJAX thread loading) — this stylesheet is written to be
   visual-only and avoid touching layout classes osTicket's JS
   measures, but always verify ticket queues, the compose/reply
   editor, and ticket detail view render correctly before rolling
   out to your agents.

6. Hard-refresh (Ctrl+Shift+R).

## Rollback
If anything looks broken, restore the two `.bak` files and hard-refresh:
```bash
cp include/client/header.inc.php.bak include/client/header.inc.php
cp include/staff/header.inc.php.bak include/staff/header.inc.php
```
