(function() {
	var MONTHS = [
		'Январь',
		'Февраль',
		'Март',
		'Апрель',
		'Май',
		'Июнь',
		'Июль',
		'Август',
		'Сентябрь',
		'Октябрь',
		'Ноябрь',
		'Декабрь'
	];

	links.Graph.StepDate.prototype.getLabelMajor = function(date) {
		if (date == undefined) {
			date = this.current;
		}

		switch (this.scale) {
			case links.Graph.StepDate.SCALE.MILLISECOND:
				return this.addZeros(date.getHours(), 2) + ':' +
					this.addZeros(date.getMinutes(), 2) + ':' +
					this.addZeros(date.getSeconds(), 2);
			case links.Graph.StepDate.SCALE.SECOND:
				return this.addZeros(date.getDate(), 2) + '.' +
					this.addZeros(date.getMonth(), 2) + ' ' +
					this.addZeros(date.getHours(), 2) + ':' +
					this.addZeros(date.getMinutes(), 2);
			case links.Graph.StepDate.SCALE.MINUTE:
				return this.addZeros(date.getDate(), 2) + '.' +
					this.addZeros(date.getMonth(), 2) + '.' +
					date.getFullYear();
			case links.Graph.StepDate.SCALE.HOUR:
				return this.addZeros(date.getDate(), 2) + '.' +
					this.addZeros(date.getMonth(), 2) + '.' +
					date.getFullYear();
			case links.Graph.StepDate.SCALE.WEEKDAY:
			case links.Graph.StepDate.SCALE.DAY:
				return MONTHS[date.getMonth()] + ' ' + date.getFullYear();
			case links.Graph.StepDate.SCALE.MONTH:
				return String(date.getFullYear());
			default:
				return '';
		}
	};
	links.Graph.StepDate.prototype.getLabelMinor = function(date) {
		if (date == undefined) {
			date = this.current;
		}

		switch (this.scale) {
			case links.Graph.StepDate.SCALE.MILLISECOND:
				return this.addZeros(date.getMilliseconds(), 3);
			case links.Graph.StepDate.SCALE.SECOND:
				return this.addZeros(date.getSeconds(), 2);
			case links.Graph.StepDate.SCALE.MINUTE:
				return this.addZeros(date.getHours(), 2) + ":"
					+ this.addZeros(date.getMinutes(), 2);
			case links.Graph.StepDate.SCALE.HOUR:
				return this.addZeros(date.getHours(), 2) + ":"
					+ this.addZeros(date.getMinutes(), 2);
			case links.Graph.StepDate.SCALE.WEEKDAY:
				return this.addZeros(date.getDate(), 2);
			case links.Graph.StepDate.SCALE.DAY:
				return this.addZeros(date.getDate(), 2);
			case links.Graph.StepDate.SCALE.MONTH:
				return MONTHS[date.getMonth()];
			case links.Graph.StepDate.SCALE.YEAR:
				return String(date.getFullYear());
			default:
				return '';
		}
	};
	links.Graph.StepDate.prototype.getCurrent = function() {
		var result = this.current;
		result.toString = function() {
			return moment(this).format('DD.MM.YYYY');
		};

		return result;
	};
	var GetPointUnit = function(number) {
		var unit = '';
		var modulo = number % 10;
		if (modulo == 1 && (number < 10 || number > 20)) {
			unit = 'пункт';
		} else if (modulo > 1 && modulo < 5 && (number < 10 || number > 20)) {
			unit = 'пункта';
		} else {
			unit = 'пунктов';
		}

		return unit;
	};

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
})();
