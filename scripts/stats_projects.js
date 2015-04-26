google.load('visualization', '1.0');
google.setOnLoadCallback(
	function() {
		var data_table = new google.visualization.DataTable();
		data_table.addColumn('date', 'start');
		data_table.addColumn('date', 'end');
		data_table.addColumn('string', 'content');
		data_table.addColumn('string', 'group');

		var data = [];
		var FormatDate = function(date) {
			var date_parts = date.split('T')[0].split('-');
			return date_parts[2] + '.' + date_parts[1] + '.' + date_parts[0];
		};
		var MakeRow = function(start, end, title, group) {
			var row = [];
			row.push(new Date(Date.parse(start)));
			row.push(new Date(Date.parse(end)));
			row.push(
				'<div class = "content" title = "' + title + '">'
					+ '<div class = "start-mark" title = "'
						+ FormatDate(start)
						+ '">'
					+'</div>'
					+ '<div class = "end-mark" title = "'
						+ FormatDate(end)
						+ '">'
					+ '</div>'
				+ '</div>'
			);
			row.push(group);
			console.log(group);

			return row;
		};
		var first_keys = Object.keys(STATS_DATA);
		for (var i = 0; i < first_keys.length; i++) {
			var first_key = first_keys[i];
			var subdata = STATS_DATA[first_key];
			console.log('*' + first_key);

			var second_keys = Object.keys(subdata);
			var first = true;
			for (var j = 0; j < second_keys.length; j++) {
				var second_key = second_keys[j];
				var subsubdata = subdata[second_key];
				console.log('*' + second_key);

				var title = first_key + ', ' + second_key;
				if (first) {
					data.push(
						MakeRow(
							subsubdata.start,
							subsubdata.end,
							title,
							first_key
						)
					);
					first = false;
				}
				data.push(
					MakeRow(
						subsubdata.start,
						subsubdata.end,
						title,
						'&#10551;' + second_key
					)
				);
			}
		}
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
