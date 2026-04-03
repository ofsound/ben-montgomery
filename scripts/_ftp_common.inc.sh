# Shared FTP settings from theme .env + curl option flags.
# Source after setting THEME_DIR to the theme root (parent of scripts/).
# Defines: FTP_HOST FTP_USER FTP_PASS FTP_PORT FTP_REMOTE_DIR
#          TLS_OPT PASV_OPT EPSV_OPT

ENV_FILE="$THEME_DIR/.env"
if [ ! -f "$ENV_FILE" ]; then
  printf '%s\n' "Missing $ENV_FILE — copy .env.example to .env and set credentials." >&2
  exit 1
fi

set -a
# shellcheck disable=SC1090
. "$ENV_FILE"
set +a

: "${FTP_HOST:?Set FTP_HOST in .env}"
: "${FTP_USER:?Set FTP_USER in .env}"
: "${FTP_PASS:?Set FTP_PASS in .env}"

FTP_PORT="${FTP_PORT:-21}"
FTP_TLS="${FTP_TLS:-0}"
FTP_PASSIVE="${FTP_PASSIVE:-1}"
FTP_DISABLE_EPSV="${FTP_DISABLE_EPSV:-0}"
FTP_REMOTE_DIR="${FTP_REMOTE_DIR:-}"

TLS_OPT=
case $FTP_TLS in
  1 | yes | true | TRUE) TLS_OPT=--ssl-reqd ;;
esac
PASV_OPT=--ftp-pasv
case $FTP_PASSIVE in
  0 | no | false | FALSE) PASV_OPT='--ftp-port -' ;;
esac
EPSV_OPT=
case $FTP_DISABLE_EPSV in
  1 | yes | true | TRUE) EPSV_OPT=--disable-epsv ;;
esac
