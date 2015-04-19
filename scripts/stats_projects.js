google.load('visualization', '1.0');
google.setOnLoadCallback(
	function() {
		var data = new google.visualization.DataTable();
		data.addColumn('date', 'start');
		data.addColumn('date', 'end');
		data.addColumn('string', 'content');
		data.addColumn('string', 'group');
		data.addRows(
			[
				[
					new Date(1600, 0, 1),
					new Date(1610, 0, 1),
					'<div class = "content" title = "Project 1">'
							+ '<div class = "start-mark" title = "Start"></div>'
							+ '<div class = "end-mark" title = "End"></div>'
						+ '</div>',
					'Project 1'
				],
				[
					new Date(1620, 0, 1),
					new Date(1630, 0, 1),
					'<div class = "content" title = "Project 1">'
							+ '<div class = "start-mark" title = "Start"></div>'
							+ '<div class = "end-mark" title = "End"></div>'
						+ '</div>',
					'Project 1'
				],
				[
					new Date(1640, 0, 1),
					new Date(1650, 0, 1),
					'<div class = "content" title = "Project 1">'
							+ '<div class = "start-mark" title = "Start"></div>'
							+ '<div class = "end-mark" title = "End"></div>'
						+ '</div>',
					'Project 1'
				],
				[
					new Date(1605, 0, 1),
					new Date(1615, 0, 1),
					'<div class = "content" title = "Project 2">'
							+ '<div class = "start-mark" title = "Start"></div>'
							+ '<div class = "end-mark" title = "End"></div>'
						+ '</div>',
					'Project 2'
				],
				[
					new Date(1625, 0, 1),
					new Date(1635, 0, 1),
					'<div class = "content" title = "Project 2">'
							+ '<div class = "start-mark" title = "Start"></div>'
							+ '<div class = "end-mark" title = "End"></div>'
						+ '</div>',
					'Project 2'
				],
				[
					new Date(1617, 6, 1),
					new Date(1627, 6, 1),
					'<div class = "content" title = "Project 3">'
							+ '<div class = "start-mark" title = "Start"></div>'
							+ '<div class = "end-mark" title = "End"></div>'
						+ '</div>',
					'Project 3'
				]
			]
		);

		var container = $('.stats-view.projects').get(0);
		var timeline = new links.Timeline(container);
		var DrawFunction = function() {
			timeline.draw(data);
		};

		$(window).resize(
			function() {
				DrawFunction();
			}
		);
		DrawFunction();
	}
);
