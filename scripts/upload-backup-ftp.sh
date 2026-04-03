#!/bin/sh
# Upload a .wpress backup to FTP (e.g. jailed ai1wm-backups user).
# Passive mode is ON by default (same as checking "Passive" / "Passive mode" in FileZilla).
# Usage: ./scripts/upload-backup-ftp.sh [path/to/file.wpress]
# Without args, uploads the newest *.wpress under wp-content/ai1wm-backups.
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname "$0")" && pwd -P)
THEME_DIR=$(CDPATH= cd -- "$SCRIPT_DIR/.." && pwd -P)
SITE_ROOT=$(CDPATH= cd -- "$THEME_DIR/../../.." && pwd -P)
BACKUPS_DIR="$SITE_ROOT/wp-content/ai1wm-backups"

# shellcheck disable=SC1091
. "$SCRIPT_DIR/_ftp_common.inc.sh"

LOCAL_FILE=${1:-}
if [ -z "$LOCAL_FILE" ]; then
  if [ ! -d "$BACKUPS_DIR" ]; then
    printf '%s\n' "No backups directory: $BACKUPS_DIR" >&2
    exit 1
  fi
  LOCAL_FILE=$(ls -t "$BACKUPS_DIR"/*.wpress 2>/dev/null | head -n 1)
  if [ -z "$LOCAL_FILE" ] || [ ! -f "$LOCAL_FILE" ]; then
    printf '%s\n' "No .wpress files in $BACKUPS_DIR (run npm run backup or npm run upload)." >&2
    exit 1
  fi
elif [ ! -f "$LOCAL_FILE" ]; then
  printf '%s\n' "Not a file: $LOCAL_FILE" >&2
  exit 1
fi

BASENAME=$(basename "$LOCAL_FILE")
case $BASENAME in
  *.wpress) ;;
  *)
    printf '%s\n' "Expected a .wpress file, got: $LOCAL_FILE" >&2
    exit 1
    ;;
esac

REMOTE_NAME=$BASENAME
if [ -n "$FTP_REMOTE_DIR" ] && [ "$FTP_REMOTE_DIR" != "." ]; then
  REMOTE_NAME="${FTP_REMOTE_DIR%/}/$BASENAME"
fi

URL="ftp://${FTP_HOST}:${FTP_PORT}/${REMOTE_NAME}"

printf 'Uploading %s -> ftp://%s:%s/%s\n' "$LOCAL_FILE" "$FTP_HOST" "$FTP_PORT" "$REMOTE_NAME"
# shellcheck disable=SC2086
curl -sS --fail $TLS_OPT $PASV_OPT $EPSV_OPT --user "$FTP_USER:$FTP_PASS" -T "$LOCAL_FILE" "$URL"
printf 'Done.\n'
