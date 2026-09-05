#!/usr/bin/env bash
# ============================================================
# osTicket Helpdesk Theme Bootstrap
#
# Downloads the REAL, official osTicket source (this repo does not
# vendor a copy of it — see README for why) into ./osticket, then
# copies this repo's theme files into place and auto-patches the
# two header includes to load them. Idempotent: safe to re-run.
#
# Usage:
#   ./deploy.sh                 # installs to ./osticket
#   ./deploy.sh /var/www/html   # installs to a specific path
# ============================================================
set -euo pipefail

TARGET_DIR="${1:-./osticket}"
OSTICKET_TAG="${OSTICKET_TAG:-v1.18.2}"   # pin a known-good release; override with env var if needed
THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/theme"

echo "== osTicket Helpdesk Theme bootstrap =="
echo "Target dir : $TARGET_DIR"
echo "osTicket   : $OSTICKET_TAG"
echo

command -v git >/dev/null || { echo "ERROR: git is required."; exit 1; }
command -v php >/dev/null || echo "WARNING: php not found on PATH — fine for this script, but you'll need it to actually run osTicket."

# --- 1) Fetch real osTicket source if not already present ---
if [ -d "$TARGET_DIR/.git" ]; then
  echo "-> osTicket already present at $TARGET_DIR, skipping clone (delete the dir to re-fetch)."
else
  echo "-> Cloning official osTicket ($OSTICKET_TAG)..."
  git clone --branch "$OSTICKET_TAG" --depth 1 https://github.com/osTicket/osTicket.git "$TARGET_DIR"
fi

# --- 2) Copy this repo's theme assets into osTicket's expected paths ---
echo "-> Installing theme files..."
mkdir -p "$TARGET_DIR/css/helpdesk" "$TARGET_DIR/js/helpdesk" "$TARGET_DIR/images/helpdesk"
cp "$THEME_DIR"/css/*.css   "$TARGET_DIR/css/helpdesk/"
cp "$THEME_DIR"/js/*.js     "$TARGET_DIR/js/helpdesk/"
[ -f "$THEME_DIR/assets/helpdesk-logo-placeholder.svg" ] && cp "$THEME_DIR/assets/helpdesk-logo-placeholder.svg" "$TARGET_DIR/images/helpdesk/"
[ -f "$THEME_DIR/assets/helpdesk-logo.png" ] && cp "$THEME_DIR/assets/helpdesk-logo.png" "$TARGET_DIR/images/helpdesk/"

# --- 3) Auto-patch the two header includes (idempotent — checks for a marker first) ---
patch_header() {
  local file="$1"
  local marker="HELPDESK-THEME-INJECT"
  local snippet="$2"

  if [ ! -f "$file" ]; then
    echo "   ! Could not find $file — skipping (osTicket may have moved this file in a newer release; patch it manually per README)."
    return
  fi
  if grep -q "$marker" "$file" ]; then
    echo "   - $file already patched, skipping."
    return
  fi

  cp "$file" "$file.bak"
  # Insert right before </head> — safest anchor across osTicket point releases
  # (falls back to end-of-file append with a warning if </head> isn't found).
  if grep -qi "</head>" "$file"; then
    php -r '
      $f = $argv[1]; $snippet = $argv[2];
      $c = file_get_contents($f);
      $c = preg_replace("/<\/head>/i", $snippet . "\n</head>", $c, 1);
      file_put_contents($f, $c);
    ' "$file" "$snippet"
    echo "   + Patched $file (backup at $file.bak)"
  else
    echo "$snippet" >> "$file"
    echo "   + Appended to $file — no </head> found, verify placement manually (backup at $file.bak)"
  fi
}

CLIENT_SNIPPET='<!-- HELPDESK-THEME-INJECT -->
<link rel="stylesheet" href="/css/helpdesk/helpdesk-tokens.css">
<link rel="stylesheet" href="/css/helpdesk/helpdesk-client-portal.css">
<script src="/js/helpdesk/helpdesk-client-portal.js" defer></script>'

STAFF_SNIPPET='<!-- HELPDESK-THEME-INJECT -->
<link rel="stylesheet" href="/css/helpdesk/helpdesk-tokens.css">
<link rel="stylesheet" href="/css/helpdesk/helpdesk-staff-panel.css">
<script src="/js/helpdesk/helpdesk-staff-panel.js" defer></script>'

echo "-> Patching client portal header..."
patch_header "$TARGET_DIR/include/client/header.inc.php" "$CLIENT_SNIPPET"

echo "-> Patching staff panel header..."
patch_header "$TARGET_DIR/include/staff/header.inc.php" "$STAFF_SNIPPET"

# --- 4) Permissions ---
echo "-> Setting permissions..."
find "$TARGET_DIR/css/helpdesk" "$TARGET_DIR/js/helpdesk" "$TARGET_DIR/images/helpdesk" -type f -exec chmod 644 {} \;
find "$TARGET_DIR/css/helpdesk" "$TARGET_DIR/js/helpdesk" "$TARGET_DIR/images/helpdesk" -type d -exec chmod 755 {} \;

echo
echo "== Done =="
echo "Next steps:"
echo "  1. Point your web server's document root at: $TARGET_DIR"
echo "  2. If osTicket itself isn't installed yet, run its web installer"
echo "     (visit the site, follow setup/ wizard) — that part is unchanged"
echo "     by this script, it only handles the theme."
echo "  3. Hard-refresh (Ctrl+Shift+R) the client portal and staff panel."
echo "  4. If /css/helpdesk/... 404s in DevTools Network tab, your web root"
echo "     path doesn't match \$TARGET_DIR — adjust the href/src paths in"
echo "     the injected <link> tags to match your actual URL structure."
