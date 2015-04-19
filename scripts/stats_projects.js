google.load('visualization', '1.0', {'packages': ['controls']});
google.setOnLoadCallback(
	function() {
		var data = new google.visualization.arrayToDataTable(
			[
				['Project 1', new Date(1600, 0, 1), new Date(1610, 0, 1)],
				['Project 1', new Date(1620, 0, 1), new Date(1630, 0, 1)],
				['Project 1', new Date(1640, 0, 1), new Date(1650, 0, 1)],
				['Project 2', new Date(1605, 0, 1), new Date(1615, 0, 1)],
				['Project 2', new Date(1625, 0, 1), new Date(1635, 0, 1)],
				['Project 3', new Date(1617, 6, 1), new Date(1627, 6, 1)]
			]
		);

		/*var formatter = new google.visualization.DateFormat({pattern: 'dd.MM.yyyy'});
		formatter.format(data, 1);
		formatter.format(data, 2);*/

		var container = $('#project-dashboard-view').get(0);
		var dashboard = new google.visualization.Dashboard(container);
		var range_filter = new google.visualization.ControlWrapper(
			{
				'controlType': 'ChartRangeFilter',
				'containerId': 'project-filter-view',
				'options': {
					'filterColumnIndex': 1,
					'range': {
						'start': new Date(1617, 0, 1),
						'end': new Date(1627, 11, 31)
					}
				}
			}
		);
		var chart = new google.visualization.ChartWrapper(
			{
				'chartType': 'Timeline',
				'containerId': 'project-chart-view',
				'options': {
					'timeline': {
						'singleColor': '#5cb85c'
					}
				}
			}
		);
		dashboard.bind(range_filter, chart);
		var DrawFunction = function() {
			dashboard.draw(data);
		};
		$(window).resize(
			function() {
				DrawFunction();
			}
		);
		DrawFunction();
	}
);
