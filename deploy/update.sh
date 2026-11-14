#!/usr/bin/env bash
#
# Updates the live site on Hostinger from this GitHub repository.
#
# Run it in the Hostinger file manager terminal (or over SSH):
#
#   bash update.sh              # deploys the "main" branch
#   bash update.sh some-branch  # deploys another branch
#
# What it does, in order:
#   1. Backs up the live files and the live database.
#   2. Downloads the chosen branch from GitHub.
#   3. Puts the site in maintenance mode.
#   4. Copies the new code over the live code. The live .env, uploaded
#      files (storage, banners, proofs) and vendor folder are kept.
#      Code files that no longer exist in the repository are moved into
#      the backup folder, not deleted.
#   5. Refreshes Composer's class list, runs database migrations, and
#      rebuilds the caches.
#   6. Brings the site back up.
#
# Nothing is deleted. To undo an update, see the "To roll back" lines the
# script prints at the end.

set -euo pipefail

BRANCH="${1:-main}"
REPO="${REPO:-https://github.com/Quimanjan/smms-hrms.git}"
SITE="${SITE:-$HOME/domains/stmarymagdalenehrms.com}"
LIVE="$SITE/public_html"
SRC="$SITE/release_src"
STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$SITE/backups/$STAMP"
COMPOSER="$(command -v composer2 || command -v composer)"

step() { printf '\n== %s\n' "$1"; }

env_value() {
    # Reads one value from the live .env without printing it.
    grep -E "^$1=" "$LIVE/.env" | head -1 | cut -d= -f2- | tr -d '\r' | sed -e 's/^"//' -e 's/"$//'
}

[ -f "$LIVE/artisan" ] || { echo "No Laravel project found in $LIVE"; exit 1; }

step "1/6 Backing up live files and database to $BACKUP"
mkdir -p "$BACKUP"
rsync -a --exclude node_modules "$LIVE/" "$BACKUP/public_html/"
MYSQL_PWD="$(env_value DB_PASSWORD)" mysqldump \
    --host="$(env_value DB_HOST)" --user="$(env_value DB_USERNAME)" \
    --single-transaction --no-tablespaces "$(env_value DB_DATABASE)" > "$BACKUP/database.sql"
[ -s "$BACKUP/database.sql" ] || { echo "Database backup is empty. Stopping before any change."; exit 1; }
echo "Files: $(du -sh "$BACKUP/public_html" | cut -f1)   Database: $(du -h "$BACKUP/database.sql" | cut -f1)"

step "2/6 Downloading branch '$BRANCH' from GitHub"
rm -rf "$SRC"
git clone --quiet --depth 1 --branch "$BRANCH" "$REPO" "$SRC"
echo "Commit: $(git -C "$SRC" log -1 --format='%h %s')"

cd "$LIVE"

on_error() {
    echo
    echo "!! The update stopped with an error. The site may still be in maintenance mode."
    echo "!! To put everything back as it was:"
    echo "     rsync -a '$BACKUP/public_html/' '$LIVE/' && cd '$LIVE' && php artisan up"
}
trap on_error ERR

step "3/6 Maintenance mode on"
php artisan down --retry=30 || true

step "4/6 Copying the new code"
# Code folders mirror the repository exactly; anything removed from the
# repository is moved to the backup folder.
for dir in app config database resources routes; do
    rsync -a --delete --backup --backup-dir="$BACKUP/removed/$dir" "$SRC/$dir/" "$LIVE/$dir/"
done
# Everything else is added or updated, never removed.
rsync -a \
    --exclude .git --exclude .env --exclude storage --exclude vendor \
    --exclude node_modules --exclude bootstrap/cache \
    --exclude app --exclude config --exclude database --exclude resources --exclude routes \
    "$SRC/" "$LIVE/"

step "5/6 Composer, migrations, caches"
# Composer's own scripts are skipped because this hosting disables the PHP
# function they need (proc_open); package discovery is run directly instead.
if ! cmp -s "$SRC/composer.lock" "$BACKUP/public_html/composer.lock"; then
    "$COMPOSER" install --no-interaction --no-scripts --prefer-dist --optimize-autoloader
else
    "$COMPOSER" dump-autoload --optimize --no-scripts --no-interaction
fi
php artisan package:discover
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
[ -e public/storage ] || php artisan storage:link || true

step "6/6 Maintenance mode off"
php artisan up
trap - ERR
rm -rf "$SRC"

echo
echo "Done. Deployed $BRANCH at $(date)."
echo "To roll back to the version from before this update:"
echo "  rsync -a '$BACKUP/public_html/' '$LIVE/'"
echo "  (database, only if needed) import '$BACKUP/database.sql' in phpMyAdmin"
