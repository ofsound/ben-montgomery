#!/bin/sh
# Verify FTP login and passive data channel using the same options as upload-backup-ftp.sh.
# Usage: ./scripts/test-ftp-connection.sh
# Verbose wire log: FTP_DEBUG=1 ./scripts/test-ftp-connection.sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname "$0")" && pwd -P)
THEME_DIR=$(CDPATH= cd -- "$SCRIPT_DIR/.." && pwd -P)
# shellcheck disable=SC1091
. "$SCRIPT_DIR/_ftp_common.inc.sh"

LIST_URL="ftp://${FTP_HOST}:${FTP_PORT}/"
printf 'Testing FTP (list directory) ftp://%s:%s/ ...\n' "$FTP_HOST" "$FTP_PORT"

DEBUG_OPT=
if [ "${FTP_DEBUG:-0}" = 1 ] || [ "${FTP_DEBUG:-0}" = yes ]; then
  DEBUG_OPT=-v
fi

# shellcheck disable=SC2086
if curl $DEBUG_OPT -sS --fail $TLS_OPT $PASV_OPT $EPSV_OPT --user "$FTP_USER:$FTP_PASS" --list-only "$LIST_URL"; then
  printf '\nFTP OK: login and passive listing succeeded.\n'
else
  printf '\nFTP failed. Try FTP_DISABLE_EPSV=1 in .env or FTP_DEBUG=1 for verbose curl.\n' >&2
  exit 1
fi
