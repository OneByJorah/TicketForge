# Client Portal — Injection Point

File to edit: `include/client/header.inc.php`

1. **Back up the file first:**
   ```bash
   cp include/client/header.inc.php include/client/header.inc.php.bak
   ```

2. Open it and find the line that loads osTicket's own stylesheet — it will
   look something like this (exact path may vary slightly by build):
   ```php
   <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/style.css?<?php echo $ost_version; ?>">
   ```

3. Immediately **after** that line, add:
   ```php
   <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/helpdesk/helpdesk-tokens.css">
   <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/helpdesk/helpdesk-client-portal.css">
   ```

4. Right before the closing `</body>` tag (usually in
   `include/client/footer.inc.php`), add:
   ```php
   <script src="<?php echo ROOT_PATH; ?>js/helpdesk/helpdesk-client-portal.js"></script>
   ```

5. Upload the theme files to:
   - `css/helpdesk/helpdesk-tokens.css`
   - `css/helpdesk/helpdesk-client-portal.css`
   - `js/helpdesk/helpdesk-client-portal.js`
   - your logo to `images/helpdesk/helpdesk-logo.png` (see LOGO.md)

6. Hard-refresh the client portal (Ctrl+Shift+R) to bust the CSS cache.

If your osTicket install renames these include files or you're on a
version where the header markup differs slightly, search for the
`<head>` tag in `include/client/header.inc.php` — the `<link>` tags
just need to land anywhere inside `<head>...</head>`, after osTicket's
own stylesheet so ours can override it.
