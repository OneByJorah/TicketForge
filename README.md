<div align="center">

# ArahOStheme

### A modern, branded theme and installable plugin for osTicket

**Premium helpdesk experience — rebuilt with the [ArahOS](https://github.com/OneByJorah/arahOS-theme) design language and the [osHelpdesk](https://github.com/OneByJorah/oshelpdesk) deployment tooling.** ArahOStheme is the unified, maintained successor to both projects: a full-width modern backend, a split-screen agent login, a polished client portal, single-login convenience, and a PWA-ready front end — for osTicket **1.18.x**.

![License](https://img.shields.io/badge/license-MIT-blue.svg)

![ArahOS Banner](docs/assets/banner.svg)

![Screenshot](docs/assets/screenshot.png)

</div>

---

## Features

### Client portal
- Modern, full-width client area with a clean card layout and a **light/dark toggle** (persisted).
- Split-screen **agent login** page with the helpdesk brand on the left, form on the right.
- **Single login redirect** — one sign-in covers both the admin and client areas.
- Knowledge-base & FAQ pages with a pre-seeded `db/kb-seed.sql` for a quick start.
- Fully **responsive** breakpoints at 380, 480, 600, 768, 900 and 1100 px (mobile-first).
- **PWA-ready**: `manifest.webmanifest`, service worker (`sw.js`), install prompt (`js/arahOS/arahOS-pwa-install.js`) and offline page.

### Staff backend
- Full-width staff panel with a refined navy-and-gold palette.
- Native-feeling admin experience on both the client and staff sides.

### Extras
- `screenshots/` for previews, `showcase/` for live HTML demos, and `preview/` for standalone mockups.
- CI workflow (`.github/workflows/ci.yml`) plus issue/PR templates.
- Two supported install paths — pick whichever fits your deployment (see below).

## Design system

| Token | Value |
|-------|-------|
| Brand navy (ArahOS) | `#0a1f44` |
| Brand gold (ArahOS) | `#f2a900` |
| Navy primary (osHelpdesk) | `#1a237e` |
| Navy secondary (osHelpdesk) | `#283593` |
| Gold primary (osHelpdesk) | `#ffb300` |
| Gold secondary (osHelpdesk) | `#ffc107` |
| Logo | 200 × 50 px |

All tokens are defined in `css/arahOS/arahOS-tokens.css` and `theme/css/helpdesk-tokens.css`.

---

## Quick install

### Option A — Plugin + template assets

```bash
# From the repo root (Ubuntu/Debian)
sudo ./install.sh /var/www/osticket/upload
```

This copies `plugin/`, `css/arahOS/`, `js/arahOS/`, overrides the staff/client header + login templates, and installs the root PWA files, then fixes ownership (`www-data:www-data`). Enable **ArahOS Help Desk Theme** in **Admin Panel → Manage → Plugins**, and optionally seed the KB via `db/kb-seed.sql`.

### Option B — osHelpdesk style + deploy script

```bash
cd /path/to/osticket
# copy theme assets into the osTicket tree
cp -r theme/* include/  # or as documented in theme/README
sudo ./deploy.sh
```

The deploy script idempotently injects the theme snippet into `include/client/header.inc.php` and `include/staff/header.inc.php` (marker-guarded, with `.bak` backups) and references it via:

```php
$cfg->style = 'ArahOStheme';
```

in `include/ost-config.php`.

### Option C — Manual (no script)

1. Copy `plugin/` → `include/plugins/`.
2. Copy `css/arahOS/` → `css/arahOS/` and `js/arahOS/` → `js/arahOS/`.
3. Copy the override templates from `include/` into the matching paths in your osTicket install (`include/client/` and `include/staff/` — see `install/CLIENT_INJECTION.md` and `install/STAFF_INJECTION.md`).
4. Copy the root PWA files (`manifest.webmanifest`, `sw.js`, `offline.html`, `.htaccess`, `index.php`).

## Hardening

- `deploy.sh` writes files as `0644` (dirs `0755`) and never overwrites user edits: injected blocks are wrapped in `HELPDESK-THEME-INJECT` markers, and a `.bak` is written before any modification.
- `SECURITY.md` describes security reporting; `CODE_OF_CONDUCT.md` sets community expectations.

---

## Project structure

```
ArahOStheme/
├── .github/
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.md
│   │   ├── config.yml
│   │   └── feature_request.md
│   ├── PULL_REQUEST_TEMPLATE.md
│   └── workflows/
│       └── ci.yml
├── .gitignore
├── .htaccess
├── CODE_OF_CONDUCT.md
├── CONTRIBUTING.md
├── deploy.sh
├── index.php
├── install.ps1
├── install.sh
├── LICENSE
├── manifest.webmanifest
├── offline.html
├── README.md
├── SECURITY.md
├── sw.js
├── css/
│   └── arahOS/
│       ├── arahOS-agent-login.css
│       ├── arahOS-client-portal.css
│       ├── arahOS-staff-panel.css
│       └── arahOS-tokens.css
├── db/
│   └── kb-seed.sql
├── docs/
│   └── assets/
│       ├── banner.svg
│       └── screenshot.png
├── include/
│   ├── client/
│   │   ├── accesslink.inc.php
│   │   ├── footer.inc.php
│   │   ├── header.inc.php
│   │   └── login.inc.php
│   └── staff/
│       ├── footer.inc.php
│       ├── header.inc.php
│       ├── login.header.php
│       └── login.tpl.php
├── install/
│   ├── CLIENT_INJECTION.md
│   └── STAFF_INJECTION.md
├── js/
│   └── arahOS/
│       ├── arahOS-client-portal.js
│       ├── arahOS-landing.js
│       ├── arahOS-pwa-install.js
│       └── arahOS-staff-panel.js
├── plugin/
│   ├── arahos-theme.php
│   ├── client.css
│   ├── plugin.php
│   └── staff.css
├── preview/
│   ├── index.html
│   └── staff-preview.html
├── screenshots/
│   ├── agent-login.png
│   ├── landing-page.png
│   └── staff-dashboard.png
├── showcase/
│   ├── agent-login.html
│   ├── index.php
│   ├── responsive.html
│   ├── staff-dashboard.html
│   └── images/
│       └── arahOS/
│           ├── arahOS-banner-logo.png
│           ├── arahOS-logo.svg
│           ├── arahOS-seal.png
│           └── pwa/
│               ├── apple-touch-icon.png
│               ├── icon-192.png
│               ├── icon-512.png
│               └── icon-maskable-512.png
├── theme/
│   ├── assets/
│   │   ├── helpdesk-logo-placeholder.svg
│   │   └── LOGO.md
│   ├── css/
│   │   ├── helpdesk-client-portal.css
│   │   ├── helpdesk-staff-panel.css
│   │   └── helpdesk-tokens.css
│   └── js/
│       ├── helpdesk-client-portal.js
│       └── helpdesk-staff-panel.js
└── (67 files total)
```

## Compatibility

| osTicket | Status |
|----------|--------|
| 1.18.x   | ✅ Fully supported |
| 1.17.x   | ⚠️ Works, untested |
| 1.16.x   | ❌ Not supported |

---

## License

MIT — see [LICENSE](LICENSE). © 2026 OneByJorah / JorahOne Networks.

## Acknowledgements

ArahOStheme merges the [arahOS-theme](https://github.com/OneByJorah/arahOS-theme) plugin and the [oshelpdesk](https://github.com/OneByJorah/oshelpdesk) deployment project into a single maintained codebase. Original work by the Virgin Islands Department of Education — Office of Information Technology.