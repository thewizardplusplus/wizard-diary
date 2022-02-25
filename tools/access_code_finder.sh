#!/usr/bin/env bash

function ShowHelp() {
	local -r script_name=`basename $0`

	echo "Using:"
	echo -e "\t$script_name [-h | --help]"
	echo ""
	echo "Options:"
	echo -e "\t-h, --help  - show help."
}

function ProcessOptions() {
	local -r option="$1"

	case "$option" in
		-h|--help)
			ShowHelp
			exit

			;;
	esac
}

function FindAccessCode() {
	local -r script_path=`dirname $0`

	cat "$script_path/../protected/runtime/application.log" \
		| grep "access code" \
		| tail -1 \
		| awk '{print $NF}'
}

function OutputToStdout() {
	local -r message="$1"

	echo "$message"
}

ProcessOptions "$@"

readonly access_code=`FindAccessCode`
OutputToStdout "$access_code"
