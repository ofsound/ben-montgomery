#!/bin/sh
set -eu

THEME_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
RUNTIME_DIR="$THEME_DIR/.playground"
PID_FILE="$RUNTIME_DIR/playground.pid"
LOG_FILE="$RUNTIME_DIR/playground.log"
PORT=${PLAYGROUND_PORT:-8888}
PHP_VERSION=${PLAYGROUND_PHP_VERSION:-8.3}
WP_VERSION=${PLAYGROUND_WP_VERSION:-6.9.4}
BLUEPRINT_FILE="$THEME_DIR/playground/blueprint.json"
CLI_ENTRY="$THEME_DIR/node_modules/@wp-playground/cli/wp-playground.js"

listener_pid() {
  lsof -nP -iTCP:"$PORT" -sTCP:LISTEN -t 2>/dev/null | head -n 1 || true
}

wait_for_listener() {
  attempts=0

  while [ "$attempts" -lt 30 ]; do
    if [ -n "$(listener_pid)" ]; then
      return 0
    fi

    attempts=$((attempts + 1))
    sleep 1
  done

  return 1
}

start() {
  if [ ! -f "$CLI_ENTRY" ]; then
    printf '%s\n' "Missing @wp-playground/cli. Run npm install first." >&2
    exit 1
  fi

  if [ ! -f "$BLUEPRINT_FILE" ]; then
    printf '%s\n' "Missing Playground blueprint: $BLUEPRINT_FILE" >&2
    exit 1
  fi

  mkdir -p "$RUNTIME_DIR"

  current_pid=$(listener_pid)
  if [ -n "$current_pid" ]; then
    printf '%s\n' "Playground already running at http://localhost:$PORT (pid $current_pid)"
    exit 0
  fi

  node "$CLI_ENTRY" server \
    --port "$PORT" \
    --php "$PHP_VERSION" \
    --wp "$WP_VERSION" \
    --blueprint "$BLUEPRINT_FILE" \
    --login \
    --experimental-multi-worker \
    --mount-dir "$THEME_DIR" /wordpress/wp-content/themes/ben-montgomery \
    >"$LOG_FILE" 2>&1 &

  echo "$!" >"$PID_FILE"

  if ! wait_for_listener; then
    printf '%s\n' "Playground failed to start. Recent log output:" >&2
    tail -n 40 "$LOG_FILE" >&2 || true
    exit 1
  fi

  printf '%s\n' "Playground running at http://localhost:$PORT"
}

status() {
  current_pid=$(listener_pid)

  if [ -n "$current_pid" ]; then
    printf '%s\n' "status: running"
    printf '%s\n' "  url: http://localhost:$PORT"
    printf '%s\n' "  pid: $current_pid"
    printf '%s\n' "  log: $LOG_FILE"
    exit 0
  fi

  printf '%s\n' "status: stopped"
  printf '%s\n' "  port: $PORT"
  printf '%s\n' "  log: $LOG_FILE"
}

stop() {
  if [ -f "$PID_FILE" ]; then
    pid=$(cat "$PID_FILE" 2>/dev/null || true)
    if [ -n "${pid:-}" ]; then
      kill "$pid" 2>/dev/null || true
      sleep 1
    fi
  fi

  current_pid=$(listener_pid)
  if [ -n "$current_pid" ]; then
    kill "$current_pid" 2>/dev/null || true
    sleep 1
  fi

  rm -f "$PID_FILE"
  printf '%s\n' "Playground stopped."
}

destroy() {
  stop
  rm -rf "$RUNTIME_DIR"
  printf '%s\n' "Playground runtime files removed."
}

case "${1:-}" in
  start)
    start
    ;;
  status)
    status
    ;;
  stop)
    stop
    ;;
  destroy)
    destroy
    ;;
  *)
    printf '%s\n' "Usage: $0 {start|status|stop|destroy}" >&2
    exit 1
    ;;
esac
