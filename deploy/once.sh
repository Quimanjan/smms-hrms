#!/bin/bash
# Run by a temporary hPanel cron job: deploys once, then does nothing until the marker changes.
MARK="$HOME/.deploy-once-${1:-default}"
[ -f "$MARK" ] && exit 0
touch "$MARK"
curl -fsSL https://raw.githubusercontent.com/Quimanjan/smms-hrms/panel-revisions/deploy/update.sh -o "$HOME/update.sh" \
  && bash "$HOME/update.sh" panel-revisions > "$HOME/update.log" 2>&1
