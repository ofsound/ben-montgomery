#!/bin/sh
set -eu

THEME_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)

exec "$THEME_DIR/bin/wp-local" bm sync-site-editor "$@"

