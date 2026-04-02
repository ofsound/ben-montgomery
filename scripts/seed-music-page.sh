#!/bin/zsh
set -eu

THEME_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd -P)
cd "$THEME_DIR"

./bin/wp-local eval "require ABSPATH . 'wp-content/themes/ben-montgomery/scripts/seed-music-page.php';"
