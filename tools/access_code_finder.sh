#!/usr/bin/env bash

no_clipboard=FALSE
quiet=FALSE

function ShowHelp() {
	local -r script_name=`basename $0`

	echo "Using:"
	echo -e "\t$script_name -h | --help"
	echo -e "\t$script_name [-c | --no-clipboard]"
	echo -e "\t$script_name [-q | --quiet]"
	echo ""
	echo "Options:"
	echo -e "\t-h, --help          - show help;"
	echo -e "\t-c, --no-clipboard  - disable a copying to clipboard;"
	echo -e "\t-q, --quiet         - disable a printing to stdout."
}

function ProcessOptions() {
	local -r option="$1"

	case "$option" in
		-h|--help)
			ShowHelp
			exit

			;;
		-c|--no-clipboard)
			no_clipboard=TRUE
			;;
		-q|--quiet)
			quiet=TRUE
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

	if [[ $quiet != "TRUE" ]]
	then
		echo "$message"
	fi
}

function CopyToClipboard() {
	local -r message="$1"

	if [[ $no_clipboard != "TRUE" ]]
	then
		printf "$message" | xclip -selection clipboard -i
	fi
}

ProcessOptions "$@"

readonly access_code=`FindAccessCode`
OutputToStdout "$access_code"
CopyToClipboard "$access_code"
