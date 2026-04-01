#!/bin/sh
set -eu

THEME_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
PLUGIN_SOURCE="$THEME_DIR/tools/wordpress-mcp-adapter-loader"
PLUGIN_TARGET="$THEME_DIR/../../plugins/wordpress-mcp-adapter-loader"
EXPECTED_LINK="../themes/ben-montgomery/tools/wordpress-mcp-adapter-loader"

if [ ! -d "$PLUGIN_SOURCE" ]; then
  printf '%s\n' "Missing repo-managed MCP plugin source at $PLUGIN_SOURCE" >&2
  exit 1
fi

if [ -L "$PLUGIN_TARGET" ]; then
  current_target=$(readlink "$PLUGIN_TARGET" || true)
  if [ "$current_target" = "$EXPECTED_LINK" ]; then
    printf '%s\n' "WordPress MCP plugin already linked to repo source."
    exit 0
  fi

  printf '%s\n' "Refusing to replace unexpected symlink: $PLUGIN_TARGET -> $current_target" >&2
  exit 1
fi

if [ -e "$PLUGIN_TARGET" ]; then
  printf '%s\n' "Refusing to replace existing plugin directory: $PLUGIN_TARGET" >&2
  printf '%s\n' "Move or remove it first, then re-run npm run mcp:plugin:install." >&2
  exit 1
fi

ln -s "$EXPECTED_LINK" "$PLUGIN_TARGET"
printf '%s\n' "Linked WordPress MCP plugin into wp-content/plugins."
