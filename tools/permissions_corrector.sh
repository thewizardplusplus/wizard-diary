#!/usr/bin/env bash

function ShowHelp() {
	local -r script_name=`basename $0`

	echo "Using:"
	echo -e "\t$script_name -h | --help"
	echo -e "\t$script_name lock <path>"
	echo -e "\t$script_name unlock <path>"
	echo -e "\t$script_name list <path>"
	echo ""
	echo "Options:"
	echo -e "\t-h, --help  - show help."
	echo ""
	echo "Commands:"
	echo -e "\tlock <path>    - set permissions 0444 to all files and 0555 to" \
		"all directories in directory on <path> [default <path>: \".\"];"
	echo -e "\tunlock <path>  - set permissions 0777 to all files and" \
		"directories in directory on <path> [default <path>: \".\"];"
	echo -e "\tlist <path>    - show list of all files and directories in" \
		"directory on <path> [default <path>: \".\"]."
}

function ShowError() {
	local -r message=$1

	echo "Error: $message."
	echo ""

	ShowHelp

	exit 1
}

function GetPath {
	local path="$1"
	if [[ -z "$path" ]]
	then
		path="."
	fi

	echo "$path"
}

function ProcessCommand {
	local -r command="$1"
	case $command in
		-h|--help)
			ShowHelp
			exit

			;;
		*)
			local -r path=`GetPath "$2"`
			case $command in
				lock)
					find "$path" -type d -exec chmod 0555 {} \;
					find "$path" -type f -exec chmod 0444 {} \;
					;;
				unlock)
					find "$path" -exec chmod 0777 {} \;
					;;
				list)
					find "$path"
					;;
				*)
					if [[ -z "$command" ]]
					then
						ShowError "command should be specified"
					else
						ShowError "invalid command \"$command\""
					fi

					;;
			esac

			;;
	esac
}

ProcessCommand "$@"
