#!/usr/bin/env bash
# ============================================================
# arahOS Help Desk — osTicket Theme installer
# Usage: sudo ./install.sh /path/to/osticket/upload
# ============================================================
set -euo pipefail

TARGET="${1:-/var/www/osticket/upload}"
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [[ ! -d "$TARGET" || ! -f "$TARGET/main.inc.php" ]]; then
  echo "ERROR: '$TARGET' does not look like an osTicket upload/ directory." >&2
  echo "Usage: sudo ./install.sh /path/to/osticket/upload" >&2
  exit 1
fi

echo "==> Installing arahOS theme into: $TARGET"

# Web-server user (owner of osTicket files)
WEBUSER="$(stat -c '%U' "$TARGET/main.inc.php")"
WEBGROUP="$(stat -c '%G' "$TARGET/main.inc.php")"
echo "    File owner detected: ${WEBUSER}:${WEBGROUP}"

backup() { [[ -f "$1" ]] && cp -a "$1" "$1.bak-$(date +%s)" && echo "    backed up $(basename "$1")"; }

# 1. Plugin
mkdir -p "$TARGET/include/plugins/arahOS-theme"
cp -f "$HERE/plugin/"* "$TARGET/include/plugins/arahOS-theme/"
echo "    plugin files copied"

# 2. CSS
mkdir -p "$TARGET/css/arahOS"
cp -f "$HERE/css/arahOS/arahOS-"*.css "$TARGET/css/arahOS/"
echo "    theme CSS copied"

# 3. JS
mkdir -p "$TARGET/js/arahOS"
cp -f "$HERE/js/arahOS/arahOS-"*.js "$TARGET/js/arahOS/"
echo "    theme JS copied"

# 4. Templates
backup "$TARGET/include/staff/login.tpl.php"
backup "$TARGET/include/staff/login.header.php"
backup "$TARGET/include/staff/header.inc.php"
backup "$TARGET/include/staff/footer.inc.php"
backup "$TARGET/include/client/header.inc.php"
backup "$TARGET/include/client/footer.inc.php"
backup "$TARGET/include/client/login.inc.php"
backup "$TARGET/include/client/accesslink.inc.php"
cp -f "$HERE/include/staff/login.tpl.php"       "$TARGET/include/staff/"
cp -f "$HERE/include/staff/login.header.php"   "$TARGET/include/staff/"
cp -f "$HERE/include/staff/header.inc.php"      "$TARGET/include/staff/"
cp -f "$HERE/include/staff/footer.inc.php"      "$TARGET/include/staff/"
cp -f "$HERE/include/client/header.inc.php"     "$TARGET/include/client/"
cp -f "$HERE/include/client/footer.inc.php"     "$TARGET/include/client/"
cp -f "$HERE/include/client/login.inc.php"      "$TARGET/include/client/"
cp -f "$HERE/include/client/accesslink.inc.php" "$TARGET/include/client/"
echo "    theme templates copied"

# 5. Landing page
backup "$TARGET/index.php"
cp -f "$HERE/index.php" "$TARGET/index.php"
echo "    landing page copied"

# 6. PWA + .htaccess + images
backup "$TARGET/manifest.webmanifest"
cp -f "$HERE/manifest.webmanifest" "$TARGET/manifest.webmanifest"
cp -f "$HERE/sw.js" "$TARGET/sw.js"
cp -f "$HERE/offline.html" "$TARGET/offline.html"
[[ -f "$TARGET/.htaccess" ]] && backup "$TARGET/.htaccess" || true
cp -f "$HERE/.htaccess" "$TARGET/.htaccess"
mkdir -p "$TARGET/images/arahOS/pwa"
cp -f "$HERE/images/arahOS/"* "$TARGET/images/arahOS/" 2>/dev/null || true
cp -f "$HERE/images/arahOS/pwa/"* "$TARGET/images/arahOS/pwa/" 2>/dev/null || true
echo "    PWA + images copied"

# 7. Showcase (optional)
cp -rf "$HERE/showcase" "$TARGET/" 2>/dev/null || true
echo "    showcase copied"

# 8. Permissions
chown -R "$WEBUSER:$WEBGROUP" "$TARGET/include/plugins/arahOS-theme"   "$TARGET/css/arahOS" "$TARGET/js/arahOS"   "$TARGET/include/staff" "$TARGET/include/client"   "$TARGET/images/arahOS"   "$TARGET/manifest.webmanifest" "$TARGET/sw.js" "$TARGET/offline.html"   "$TARGET/.htaccess" "$TARGET/index.php"   "$TARGET/showcase" 2>/dev/null || true
find "$TARGET/include/plugins/arahOS-theme" "$TARGET/css/arahOS" "$TARGET/js/arahOS"   "$TARGET/include/staff" "$TARGET/include/client"   "$TARGET/images/arahOS" "$TARGET/showcase"   -type f -exec chmod 644 {} \; 2>/dev/null || true
echo "    permissions set"

cat <<EOFC

==> Installation complete!

Next steps:
    1. Admin Panel → Manage → Plugins → enable 'arahOS Help Desk Theme'
    2. (Optional) mysql -u <user> -p <db> < $HERE/db/kb-seed.sql
    3. Visit /showcase/responsive.html to see the theme at phone/tablet/laptop sizes

EOFC
