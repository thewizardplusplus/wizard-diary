google.load('visualization', '1.0');
google.setOnLoadCallback(
	function() {
		if (STATS_DATA.length == 0) {
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
		var MakeRow = function(start, end, title, group) {
			var row = [];
			row.push(new Date(Date.parse(start)));
			row.push(new Date(Date.parse(end)));
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
		var first_keys = Object.keys(STATS_DATA);
		var first_keys_length = first_keys.length;
		for (var i = 0; i < first_keys_length; i++) {
			var first_key = first_keys[i];
			var subdata = STATS_DATA[first_key];
			data.push(
				MakeRow(
					subdata.start,
					subdata.end,
					first_key,
					FormatNumber(i, first_keys_length) + '._ | ' + first_key
				)
			);

			var second_keys = Object.keys(subdata.tasks);
			var second_keys_length = second_keys.length;
			for (var j = 0; j < second_keys_length; j++) {
				var second_key = second_keys[j];
				var subsubdata = subdata.tasks[second_key];

				var title = first_key + ', ' + second_key;
				var group = '&#10551;' + second_key;
				for (var k = 0; k < subsubdata.intervals.length; k++) {
					var interval = subsubdata.intervals[k];
					data.push(
						MakeRow(
							interval.start,
							interval.end,
							title,
							FormatNumber(i, first_keys_length) + '.'
								+ FormatNumber(j, second_keys_length) + ' | '
								+ group
						)
					);
				}
			}
		}

		var data_table = new google.visualization.DataTable();
		data_table.addColumn('date', 'start');
		data_table.addColumn('date', 'end');
		data_table.addColumn('string', 'content');
		data_table.addColumn('string', 'group');
		data_table.addRows(data);

		var container = $('.stats-view.projects').get(0);
		var timeline = new links.Timeline(container, {locale: 'ru'});
		var DrawFunction = function() {
			timeline.draw(data_table);
		};

		$(window).resize(
			function() {
				DrawFunction();
			}
		);
		DrawFunction();
	}
);
