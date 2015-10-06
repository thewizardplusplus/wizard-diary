(function() {
	var options = {
		MONTHS: [
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
		]
	};

	var GetLabelMajor = function(options, date) {
		if (date == undefined) {
			date = this.current;
		}

		switch (this.scale) {
			case options.UNITS.MILLISECOND:
				return this.addZeros(date.getHours(), 2) + ':' +
					this.addZeros(date.getMinutes(), 2) + ':' +
					this.addZeros(date.getSeconds(), 2);
			case options.UNITS.SECOND:
				return this.addZeros(date.getDate(), 2) + '.' +
					this.addZeros(date.getMonth(), 2) + ' ' +
					this.addZeros(date.getHours(), 2) + ':' +
					this.addZeros(date.getMinutes(), 2);
			case options.UNITS.MINUTE:
				return this.addZeros(date.getDate(), 2) + '.' +
					this.addZeros(date.getMonth(), 2) + '.' +
					date.getFullYear();
			case options.UNITS.HOUR:
				return this.addZeros(date.getDate(), 2) + '.' +
					this.addZeros(date.getMonth(), 2) + '.' +
					date.getFullYear();
			case options.UNITS.WEEKDAY:
			case options.UNITS.DAY:
				return options.MONTHS[date.getMonth()] + ' ' +
					date.getFullYear();
			case options.UNITS.MONTH:
				return String(date.getFullYear());
			default:
				return '';
		}
	};
	var GetLabelMinor = function(options, date) {
		if (date == undefined) {
			date = this.current;
		}

		switch (this.scale) {
			case options.UNITS.MILLISECOND:
				return this.addZeros(date.getMilliseconds(), 3);
			case options.UNITS.SECOND:
				return this.addZeros(date.getSeconds(), 2);
			case options.UNITS.MINUTE:
				return this.addZeros(date.getHours(), 2) + ":"
					+ this.addZeros(date.getMinutes(), 2);
			case options.UNITS.HOUR:
				return this.addZeros(date.getHours(), 2) + ":"
					+ this.addZeros(date.getMinutes(), 2);
			case options.UNITS.WEEKDAY:
				return this.addZeros(date.getDate(), 2);
			case options.UNITS.DAY:
				return this.addZeros(date.getDate(), 2);
			case options.UNITS.MONTH:
				return options.MONTHS[date.getMonth()];
			case options.UNITS.YEAR:
				return String(date.getFullYear());
			default:
				return '';
		}
	};

	if (links.Graph) {
		$.extend(options, {UNITS: links.Graph.StepDate.SCALE});

		links.Graph.StepDate.prototype.getLabelMajor = function(date) {
			return GetLabelMajor.call(this, options, date);
		};
		links.Graph.StepDate.prototype.getLabelMinor = function(date) {
			return GetLabelMinor.call(this, options, date);
		};
		links.Graph.StepDate.prototype.getCurrent = function() {
			var result = this.current;
			result.toString = function() {
				return moment(this).format('DD.MM.YYYY');
			};

			return result;
		};
	}

	if (links.Timeline) {
		links.Timeline.StepDate.prototype.getLabelMajor =
			function(options, date) {
				$.extend(options, {UNITS: links.Timeline.StepDate.SCALE});
				return GetLabelMajor.call(this, options, date);
			};
		links.Timeline.StepDate.prototype.getLabelMinor =
			function(options, date) {
				$.extend(options, {UNITS: links.Timeline.StepDate.SCALE});
				return GetLabelMinor.call(this, options, date);
			};
	}
})();
