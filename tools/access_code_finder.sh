#!/usr/bin/env bash

declare -r script_path="$(dirname "$0")"

declare -r access_code="$(
	cat "$script_path/../protected/runtime/application.log" \
		| grep "access code" \
		| tail -1 \
		| awk '{print $NF}'
)"
echo "info: detected the access code \"$access_code\""
