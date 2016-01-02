google.load('visualization', '1.0');
google.setOnLoadCallback(
	function() {
		if (STATS_DATA.length == 0) {
			$('.mean-view').hide();
			$('.empty-label').show();

			return;
		}

		var dates = Object.keys(STATS_DATA);

		var data = [];
		var ParseDate = function(date_string) {
			return new Date(Date.parse(date_string));
		};
		for (var i = 0; i < dates.length; i++) {
			var date_string = dates[i];
			var date = ParseDate(date_string);
			var value = STATS_DATA[date_string];
			var row = [
				date,
				value.not_canceled,
				value.total,
				value.satisfied,
				value.quality
			];
			data.push(row);
		}

		var data_table = new google.visualization.DataTable();
		data_table.addColumn('date', 'date');
		data_table.addColumn('number', 'not canceled');
		data_table.addColumn('number', 'total');
		data_table.addColumn('number', 'satisfied');
		data_table.addColumn('number', 'quality');
		data_table.addRows(data);

		var minimal_date = Date.now();
		var maximal_date = Date.now();
		if (dates.length) {
			minimal_date = ParseDate(dates[0]);

			maximal_date = ParseDate(dates[dates.length - 1]);
			maximal_date.setDate(maximal_date.getDate() + 1);
		}

		var options = {
			legend: {visible: false},
			line: {style: 'dot-line'},
			lines: [
				{color: '#808080'},
				{color: '#333333'},
				{color: '#5cb85c', width: 4, radius: 4},
				{color: '#da70d6', width: 4, radius: 4}
			],
			min: minimal_date,
			max: maximal_date,
			// 5 days
			zoomMin: 5 * 24 * 60 * 60 * 1000,
			tooltip: function(point) {
				var date = moment(point.date).format('DD.MM.YYYY');
				var real_value = point.value / 10;

				var value_title = '';
				switch (point.line) {
					case 0:
						value_title =
							'Неотменённых: '
							+ real_value
							+ ' '
							+ GetPointUnit(real_value)
							+ '.';
						break;
					case 1:
						value_title =
							'Всего: '
							+ real_value
							+ ' '
							+ GetPointUnit(real_value)
							+ '.';
						break;
					case 2:
						value_title = 'Выполнено: ' + point.value + '%.';
						break;
					case 3:
						value_title = 'Уровень: ' + point.value + '.';
						break;
				}

				return '<div>Дата: ' + date + '.</div>'
					+ '<div>' + value_title + '</div>';
			}
		};
		var container = $('.stats-view.daily-points').get(0);
		var graph = new links.Graph(container);
		var DrawFunction = function() {
			graph.draw(data_table, options);
			graph.setVisibleChartRange(
				// 12 days ago
				new Date(Date.now() - 12 * 24 * 60 * 60 * 1000),
				maximal_date
			);

			$('.graph-axis-button:nth-child(1)').attr(
				'title',
				'Вертикальный масштаб +'
			);
			$('.graph-axis-button:nth-child(2)').attr(
				'title',
				'Вертикальный масштаб -'
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
