#!/bin/bash

function ProcessBackup {
	local -r backup_name=$1;

	echo -e "\tPrepare data..." >&2
	local -r backup_data=$(unzip -p $backup_name $backup_name/database_dump.sql)
	local -r points_data=$(echo "$backup_data" | grep "^\s\?('[0-9]\+',\s\?'[0-9]\+-[0-9]\+-[0-9]\+'")
	local -r dates=$(echo "$points_data" | sed "s/.*'\([0-9]\+-[0-9]\+-[0-9]\+\)'.*/\1/" | uniq)
	local -r dates_count=$(echo "$dates" | wc -l)

	echo "<?xml version = \"1.0\" encoding = \"utf-8\" ?>"
	echo "<diary>"

	local date_index=1
	for date in ${dates[@]}
	do
		local progress=$((100 * date_index / dates_count))
		local date_index=$((date_index + 1))
		echo -e "\tProcessing day $date ($progress%)..." >&2

		echo -e "\t<day date = \"$date\">"

		local date_points=$(echo "$points_data" | grep $date)
		local date_points_count=$(echo "$date_points" | wc -l)
		local filtered_date_points=$(echo "$date_points" | sed "s/.*'[0-9]\+-[0-9]\+-[0-9]\+',\s'\(.*\)',\s'\(INITIAL\|SATISFIED\|NOT_SATISFIED\|CANCELED\)',\s'\([01]\)'.*/\1 \2 \3/")
		local point_index=1
		for point in ${filtered_date_points[@]}
		do
			local progress=$((100 * point_index / date_points_count))
			local point_index=$((point_index + 1))
			echo -e "\t\tProcessing point ($progress%)..." >&2

			local state=$(echo "$point" | awk '{ print $(NF - 1); }')
			local check=$(echo "$point" | awk '{ print $(NF); }' | sed "s/0/false/;s/1/true/")
			local text=$(echo "$point" | awk '{ for (i = 1; i < NF - 1; i++) { printf "%s", $i OFS; } printf ORS; }' | sed "s/\s*$//" | base64 -w 0)

			echo -e "\t\t<point state = \"$state\" check = \"$check\">$text</point>"
		done

		echo -e "\t</day>"
	done

	echo "</diary>"
}

IFS=$'\n'
readonly backup_list=$(find *.zip)
for backup in ${backup_list[@]}
do
	backup_name=$(basename $backup .zip)
	echo "Processing $backup_name..." >&2

	new_backup_name=$(echo $backup_name | sed "s/backup\(.*\)/database_dump\1.xml/")
	ProcessBackup $backup_name > $new_backup_name
done
