#!/usr/bin/env bash

declare -r script_path="$(dirname "$0")"

declare -r host_log_path="$script_path/../protected/runtime/application.log"
declare -r docker_log_path="$script_path/../data/wizard-diary/runtime/application.log"

declare selected_log=""
if [[ -f "$docker_log_path" ]]; then
	selected_log="$docker_log_path"
elif [[ -f "$host_log_path" ]]; then
	selected_log="$host_log_path"
else
	echo "error: none of the log files exist" >&2
	exit 1
fi

echo "info: using the log file \"./$(realpath --relative-to=. "$selected_log")\"" >&2

declare -r access_code="$(
	cat "$selected_log" \
		| grep "access code" \
		| tail -1 \
		| awk '{print $NF}'
)"
echo "info: detected the access code \"$access_code\""
if command -v xclip >/dev/null 2>&1; then
	echo -n "$access_code" | xclip -selection clipboard
fi
