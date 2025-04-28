#!/usr/bin/env bash

declare -r script_path="$(dirname "$0")"

declare -r user_id="$(docker compose exec wizard-diary id --user)"
declare -r group_id="$(docker compose exec wizard-diary id --group)"
chown --recursive "$user_id:$group_id" "$script_path/../data/wizard-diary"
