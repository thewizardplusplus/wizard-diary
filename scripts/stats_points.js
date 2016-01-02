google.load('visualization', '1.0');
google.setOnLoadCallback(
	function() {
		if (STATS_DATA.length == 0) {
			$('.mean-view').hide();
			$('.empty-label').show();

			return;
		}

		var data = [];
		var ParseDate = function(date_string) {
			return new Date(Date.parse(date_string));
		};
		for (var i = 0; i < STATS_DATA.length; i++) {
			var item = STATS_DATA[i];
			var date = ParseDate(item.date);
			var row = [date, parseInt(item.number)];
			data.push(row);
		}

		var data_table = new google.visualization.DataTable();
		data_table.addColumn('date', 'date');
		data_table.addColumn('number', 'points');
		data_table.addRows(data);

		var minimal_date = Date.now();
		var maximal_date = Date.now();
		if (STATS_DATA.length) {
			minimal_date = ParseDate(STATS_DATA[0].date);

			maximal_date = ParseDate(STATS_DATA[STATS_DATA.length - 1].date);
			maximal_date.setDate(maximal_date.getDate() + 1);
		}

		var options = {
			legend: {visible: false},
			line: {color: '#5cb85c', style: 'dot-line'},
			min: minimal_date,
			max: maximal_date,
			// 5 days
			zoomMin: 5 * 24 * 60 * 60 * 1000,
			tooltip: function(point) {
				var date = moment(point.date).format('DD.MM.YYYY');
				return '<div>Дата: ' + date + '.</div>'
					+ '<div>Всего: '
						+ point.value
						+ ' '
						+ GetPointUnit(point.value)
					+ '.</div>';
			}
		};
		var container = $('.stats-view.points').get(0);
		var graph = new links.Graph(container);
		var DrawFunction = function() {
			graph.draw(data_table, options);
			graph.setVisibleChartRange(
				// 12 + 1 days ago
				new Date(maximal_date.getTime() - 13 * 24 * 60 * 60 * 1000),
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
