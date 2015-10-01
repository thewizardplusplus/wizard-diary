google.load('visualization', '1.0');
google.setOnLoadCallback(
	function() {
		if (STATS_DATA.length == 0) {
			$('.empty-label').show();
			return;
		}

		var data = [];
		for (var i = 0; i < STATS_DATA.length; i++) {
			var item = STATS_DATA[i];
			var date = new Date(Date.parse(item.date));
			var row = [date, parseInt(item.number)];
			data.push(row);
		}

		var data_table = new google.visualization.DataTable();
		data_table.addColumn('date', 'date');
		data_table.addColumn('number', 'points');
		data_table.addRows(data);

		var options = {
			legend: {visible: false},
			line: {color: '#5cb85c'},
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
