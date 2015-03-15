#!/usr/bin/env bash

function ShowHelp() {
	local -r script_name=`basename $0`

	echo "Using:"
	echo -e "\t$script_name <command> <path>"
	echo ""
	echo "Commands:"
	echo -e "\tlock - set permissions" \
		"0444 to all files" \
		"and 0555 to all directories" \
		"in directory on <path>;"
	echo -e "\tunlock - set permissions" \
		"0777 to all files and directories" \
		"in directory on <path>."
}

function ShowError() {
	local -r message=$1

	echo "Error! $message"
	echo ""

	ShowHelp
}

readonly command=$1
if [[ $command != "lock" && $command != "unlock" ]]
then
	ShowError "Invalid command."
fi

readonly path="$2"
if [[ -z "$path" ]]
then
	ShowError "Empty path."
fi

case $command in
	lock)
		find "$path" -type d -exec chmod 0555 {} \;
		find "$path" -type f -exec chmod 0444 {} \;
		;;
	unlock)
		find "$path" -exec chmod 0777 {} \;
		;;
esac
