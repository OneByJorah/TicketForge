# Logo

Drop your official helpdesk logo (PNG or SVG, transparent background,
~200x60px works well) at:

```
images/helpdesk/helpdesk-logo.png
```

Then in `include/client/header.inc.php` (client) and
`include/staff/header.inc.php` (staff), find the `<img>` tag for the
site logo and point its `src` at that path, e.g.:

```php
<img src="<?php echo ROOT_PATH; ?>images/helpdesk/helpdesk-logo.png" alt="Help Desk" class="logo">
```

The theme CSS already sizes `.logo` / `img.logo` to fit the header
(`max-height: 40px` client, `36px` staff) — no extra CSS needed once
you swap the file in.

If you send me the actual logo file and your official hex codes
(navy/gold or whatever the real brand sheet says), I can regenerate
`helpdesk-tokens.css` to match exactly instead of the placeholder palette.
