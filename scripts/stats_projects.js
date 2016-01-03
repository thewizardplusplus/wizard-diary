google.load('visualization', '1.0');
google.setOnLoadCallback(
	function() {
		if (STATS_DATA.data.length == 0) {
			$('.empty-label').show();
			return;
		}

		var FormatNumber = function(number, maximum) {
			var reversed_number = maximum - number;
			var string_reversed_number = reversed_number.toString();

			var string_maximum = maximum.toString();
			while (string_maximum.length - string_reversed_number.length > 0) {
				string_reversed_number = '0' + string_reversed_number;
			}

			return string_reversed_number;
		};
		var FormatDate = function(date) {
			var date_parts = date.split('T')[0].split('-');
			return date_parts[2] + '.' + date_parts[1] + '.' + date_parts[0];
		};
		var FormatInterval = function(start, end) {
			var result = '';
			var string_start = FormatDate(start);
			var string_end = FormatDate(end);
			if (string_start != string_end) {
				result = FormatDate(start) + ' - ' + FormatDate(end)
			} else {
				result = FormatDate(start);
			}

			var days =
				Math.floor(
					(Date.parse(end) - Date.parse(start))
					/ (1000 * 60 * 60 * 24)
				)
				+ 1;
			switch (days % 10) {
				case 0:
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
					result += ', ' + days + ' дней';
					break;
				case 1:
					if (days < 11 || days > 19) {
						result += ', ' + days + ' день';
					} else {
						result += ', ' + days + ' дней';
					}

					break;
				case 2:
				case 3:
				case 4:
					if (days < 11 || days > 19) {
						result += ', ' + days + ' дня';
					} else {
						result += ', ' + days + ' дней';
					}

					break;
			}

			return result;
		};
		var ParseDate = function(date_string) {
			return new Date(Date.parse(date_string));
		};
		var MakeRow = function(start, end, title, group) {
			var row = [];
			row.push(ParseDate(start));
			row.push(ParseDate(end));
			row.push(
				'<div '
					+ 'class = "content" '
					+ 'title = "' + FormatInterval(start, end) + '">'
				+ '</div>'
			);
			row.push(group);

			return row;
		};

		var data = [];
		var groups = {};
		var first_keys = Object.keys(STATS_DATA.data);
		var first_keys_length = first_keys.length;
		for (var i = 0; i < first_keys_length; i++) {
			var first_key = first_keys[i];
			var subdata = STATS_DATA.data[first_key];

			var second_keys = Object.keys(subdata.tasks);
			var second_keys_length = second_keys.length;
			for (var j = 0; j < second_keys_length; j++) {
				var second_key = second_keys[j];
				var subsubdata = subdata.tasks[second_key];

				var title = first_key + ', ' + second_key;
				var group = '&#x21b3; ' + second_key;
				if (!groups.hasOwnProperty(group)) {
					groups[group] = 0;
				} else {
					groups[group] += 1;

					var number_of_group_duplicates = groups[group];
					for (var n = 0; n < number_of_group_duplicates; n++) {
						group += '\u200b';
					}
				}

				for (var k = 0; k < subsubdata.intervals.length; k++) {
					var interval = subsubdata.intervals[k];
					data.push(
						MakeRow(interval.start, interval.end, title, group)
					);
				}
			}

			data.push(
				MakeRow(subdata.start, subdata.end, first_key, first_key)
			);
		}

		var data_table = new google.visualization.DataTable();
		data_table.addColumn('date', 'start');
		data_table.addColumn('date', 'end');
		data_table.addColumn('string', 'content');
		data_table.addColumn('string', 'group');
		data_table.addRows(data);

		var container = $('.stats-view.projects').get(0);
		var end_date = ParseDate(STATS_DATA.end);
		var timeline = new links.Timeline(
			container,
			{
				min: ParseDate(STATS_DATA.start),
				max: end_date,
				// 69 days (the maximum scale at which the layout remains true)
				zoomMin: 69 * 24 * 60 * 60 * 1000,
				// 69 days (the maximum scale at which the layout remains true)
				zoomMax: 69 * 24 * 60 * 60 * 1000,
				groupsOrder: false,
				locale: 'ru'
			}
		);
		var DrawFunction = function() {
			timeline.draw(data_table);
			timeline.setVisibleChartRange(
				// 24 days from the end date
				new Date(end_date.getTime() - 24 * 24 * 60 * 60 * 1000),
				end_date
			);
		};

		$(window).resize(
			function() {
				DrawFunction();
			}
		);
		DrawFunction();
	}
);
