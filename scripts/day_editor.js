$(document).ready(
	function() {
		// var DICTIONARY_NAME = 'ru_RU';
		//
		// var aff_data = Typo.prototype._readFile(
		// 	DICTIONARY_BASE_PATH
		// 		+ DICTIONARY_NAME
		// 		+ '/'
		// 		+ DICTIONARY_NAME
		// 		+ '.utf_8.aff',
		// 	'UTF-8'
		// );
		// var dic_data = Typo.prototype._readFile(
		// 	DICTIONARY_BASE_PATH
		// 		+ DICTIONARY_NAME
		// 		+ '/'
		// 		+ DICTIONARY_NAME
		// 		+'.utf_8.dic',
		// 	'UTF-8'
		// );
		// var typo = new Typo(DICTIONARY_NAME, aff_data, dic_data);
		// console.log(typo.check('тестт'));
		// console.log(typo.check('тест'));

		var lang_tools = ace.require('ace/ext/language_tools');

		var day_editor = ace.edit('day-editor');
		day_editor.$blockScrolling = Infinity;
		day_editor.setTheme('ace/theme/pastel_on_dark');
		day_editor.getSession().setMode('ace/mode/wizard_diary');
		day_editor.setShowInvisibles(true);
		day_editor.setShowPrintMargin(false);
		day_editor.setOptions(
			{
				enableBasicAutocompletion: true,
				enableLiveAutocompletion: true
			}
		);
		day_editor.focus();
		day_editor.gotoLine(
			LINE,
			day_editor.getSession().getLine(LINE - 1).length
		);

		var ExtendImport = function(points_description) {
			var last_line_blocks = [];
			var extended_lines =
				points_description
				.split('\n')
				.map(
					function(line) {
						var extended_line = '';
						while (line.substr(0, 4) == '    ') {
							if (last_line_blocks.length > 0) {
								extended_line +=
									last_line_blocks.shift()
									+ ', ';
							}

							line = line.substr(4);
						}
						extended_line += line;

						if (extended_line.length > 0) {
							last_line_blocks =
								extended_line
								.split(',')
								.map(
									function(level) {
										return level.trim();
									}
								);
						}

						return extended_line;
					}
				);

			return extended_lines;
		};
		var GetLinePrefixParts = function(session, position) {
			var points_description = day_editor.getValue();
			var full_line = ExtendImport(points_description)[position.row];
			var full_line_parts = full_line.split(', ');

			var column = position.column;
			var real_line = session.getLine(position.row);
			while (real_line.substr(0, 4) == '    ' && full_line_parts.length) {
				column +=
					full_line_parts.shift().length
					+ 2 /* comma and space */
					- 4 /* indent */;
				real_line = real_line.substr(4);
			}

			var line_prefix = full_line.substr(0, column);
			var line_prefix_parts =
				line_prefix
				.split(',')
				.map(
					function(part) {
						return part.trim();
					}
				);

			return line_prefix_parts;
		};
		var GetProperty = function(object, property) {
			return object.hasOwnProperty(property) ? object[property] : [];
		};
		var GetAlternatives = function(line_prefix_parts) {
			var alternatives = [];
			switch (line_prefix_parts.length) {
				case 1:
					alternatives = Object.keys(POINT_HIERARCHY.hierarchy);
					break;
				case 2:
					alternatives = GetProperty(
						POINT_HIERARCHY.hierarchy,
						line_prefix_parts[0]
					);

					break;
				default:
					alternatives = $.map(
						POINT_HIERARCHY.tails,
						function(counter, tail) {
							return {value: tail + ' ', score: counter};
						}
					);
			}
			alternatives = alternatives.map(
				function(alternative) {
					if (typeof alternative === 'string') {
						alternative = {value: alternative + ', '};
					}

					return $.extend(alternative, {meta: 'global'});
				}
			);

			return alternatives;
		};
		lang_tools.addCompleter(
			{
				getCompletions: function(
					editor,
					session,
					position,
					prefix,
					callback
				) {
					var line_prefix_parts = GetLinePrefixParts(
						session,
						position
					);
					var alternatives = GetAlternatives(line_prefix_parts);
					callback(null, alternatives);
				}
			}
		);

		var day_mobile_editor = $('#day-mobile-editor');
		var previous_mobile_editor_content = day_mobile_editor.val();

		var FormatPoints = function(points, cursor_position) {
			points = points.map(
				function(point, index) {
					if (
						typeof cursor_position == 'undefined'
						|| cursor_position.row != index
					) {
						return point
							.replace(/((?!\s).)\s{2,}(?=\S)/g, '$1 ')
							.replace(/\s+$/, '');
					} else {
						return point;
					}
				}
			);

			while (
				points.length
				&& points[0].trim().length == 0
				&& (typeof cursor_position == 'undefined'
				|| cursor_position.row > 0)
			) {
				points.shift();
				if (typeof cursor_position != 'undefined') {
					cursor_position.row--;
				}
			}

			var new_points = [];
			var previous_point_length = 0;
			for (var i = 0; i < points.length; i++) {
				var point = points[i];
				var point_length = point.trim().length;
				if (
					point_length > 0
					|| previous_point_length > 0
					|| (typeof cursor_position != 'undefined'
					&& cursor_position.row == i)
				) {
					new_points.push(point);
				} else if (typeof cursor_position != 'undefined') {
					if (cursor_position.row > i) {
						cursor_position.row--;
					}
				}

				previous_point_length = point_length;
			}
			points = new_points;

			while (
				points.length
				&& points.slice(-1)[0].trim().length == 0
				&& (typeof cursor_position == 'undefined'
				|| cursor_position.row + 1 < points.length)
			) {
				points.pop();
			}
			if (points.length && points.slice(-1)[0].length != 0) {
				points.push('');
			}

			if (typeof cursor_position != 'undefined') {
				return {
					points: points,
					cursor_position: cursor_position
				};
			} else {
				return points;
			}
		};
		var FormatPointsDescription = function(
			points_description,
			cursor_position
		) {
			var points = points_description.split('\n');
			var result = FormatPoints(points, cursor_position);
			if (typeof cursor_position != 'undefined') {
				points_description = result.points.join('\n');
			} else {
				points_description = result.join('\n');
			}

			return {
				points_description: points_description,
				cursor_position: result.cursor_position
			};
		};
		day_editor.formatContent = function() {
			var points_description = this.getValue();
			var cursor_position = this.getCursorPosition();
			var result = FormatPointsDescription(
				points_description,
				cursor_position
			);

			this.setValue(result.points_description, -1);
			if (typeof result.cursor_position != 'undefined') {
				this.moveCursorToPosition(result.cursor_position);
			}
		};
		day_mobile_editor.formatContent = function() {
			var points_description = this.val();
			var result = FormatPointsDescription(points_description);
			this.val(result.points_description);
			previous_mobile_editor_content = result.points_description;
		};

		var saved_flag_container = $('.saved-flag');
		var saved_flag_icon = $('span', saved_flag_container);
		var is_saved = true;
		var SetSavedFlag = function(saved) {
			is_saved = saved;

			if (saved) {
				saved_flag_container
					.addClass('label-success')
					.removeClass('label-danger');
				saved_flag_icon
					.addClass('glyphicon-floppy-saved')
					.removeClass('glyphicon-floppy-remove');

				var last_symbol = document.title.slice(-1);
				if (last_symbol.length && last_symbol == '*') {
					document.title = document.title.slice(0, -1);
				}
			} else {
				saved_flag_container
					.addClass('label-danger')
					.removeClass('label-success');
				saved_flag_icon
					.addClass('glyphicon-floppy-remove')
					.removeClass('glyphicon-floppy-saved');

				var last_symbol = document.title.slice(-1);
				if (last_symbol.length && last_symbol != '*') {
					document.title += '*';
				}
			}
		};

		var number_of_points_view = $('.number-of-points-view');
		var SetNumberOfPoints = function(points_description) {
			var points =
				points_description
				.split('\n')
				.filter(
					function(line) {
						return line.trim().length != 0;
					}
				);

			var number_of_points = points.length;
			number_of_points_view.text(
				number_of_points.toString()
				+ ' '
				+ GetPointUnit(number_of_points)
			);
		};

		day_editor.on(
			'change',
			function() {
				SetSavedFlag(false);

				var points_description = day_editor.getValue();
				SetNumberOfPoints(points_description);
			}
		);
		day_editor.on(
			'paste',
			function(event) {
				event.text =
					event.text
					.trim()
					.replace(/;$/, '')
					.replace(/\u00ab([^\u00bb]*)\u00bb/, '"$1"')
					.replace(/\s\u2014\s/, ' - ')
					.replace(/\s\u27a4\s/, ' -> ');
			}
		);

		day_mobile_editor.on(
			'keyup',
			function() {
				var points_description = day_mobile_editor.val();
				if (points_description == previous_mobile_editor_content) {
					return;
				}

				SetSavedFlag(false);
				SetNumberOfPoints(points_description);

				previous_mobile_editor_content = points_description;
			}
		);
		$('a[data-toggle="tab"]').on(
			'show.bs.tab',
			function(event) {
				var backupped_saved_flag = is_saved;

				var target = $(event.target).attr('href').slice(1);
				switch (target) {
					case 'default':
						var points_description = day_mobile_editor.val();
						day_editor.setValue(points_description, -1);

						break;
					case 'mobile':
						var points_description = day_editor.getValue();
						day_mobile_editor.val(points_description);
						previous_mobile_editor_content = points_description;

						break;
				}

				SetSavedFlag(backupped_saved_flag);
			}
		);

		var save_button = $('.save-day-button');
		var save_url = save_button.data('save-url');
		var processing_animation_image = $('img', save_button);
		var save_icon = $('span', save_button);
		var day_editor_container = $(day_editor.container);
		var GetActiveEditor = function() {
			return $('.tab-pane.active').attr('id');
		};
		var FinishAnimation = function() {
			save_button.prop('disabled', false);
			processing_animation_image.hide();
			save_icon.show();

			var active_editor = GetActiveEditor();
			switch (active_editor) {
				case 'default':
					day_editor_container.removeClass('wait');
					break;
				case 'mobile':
					day_mobile_editor.removeClass('wait');
					break;
			}
		};
		var SaveViaAjax = function(callback) {
			save_button.prop('disabled', true);
			processing_animation_image.show();
			save_icon.hide();

			var points_description = '';
			var active_editor = GetActiveEditor();
			switch (active_editor) {
				case 'default':
					day_editor_container.addClass('wait');
					day_editor.formatContent();
					points_description = day_editor.getValue();

					break;
				case 'mobile':
					day_mobile_editor.addClass('wait');
					day_mobile_editor.formatContent();
					points_description = day_mobile_editor.val();

					break;
			}

			var data = $.extend(
				{'points_description': points_description},
				CSRF_TOKEN
			);
			$.post(
				save_url,
				data,
				function() {
					SetSavedFlag(true);
					FinishAnimation();

					if (typeof callback !== 'undefined') {
						callback();
					}
				}
			).fail(
				function(xhr, text_status) {
					FinishAnimation();
					AjaxErrorDialog.handler(xhr, text_status);
				}
			);
		};
		save_button.click(
			function() {
				SaveViaAjax();
			}
		);
		$(window).keydown(
			function(event) {
				if (
					(event.ctrlKey || event.metaKey)
					&& String.fromCharCode(event.which).toLowerCase() == 's'
				) {
					event.preventDefault();
					SaveViaAjax();
				}
			}
		);

		var close_button = $('.close-button');
		var day_date = close_button.data('date');
		var day_my_date = close_button.data('my-date');
		var view_url = close_button.data('view-url');
		var CloseDayEditor = function() {
			location.href = view_url;
		};
		close_button.click(
			function() {
				if (!is_saved) {
					CloseDialog.show(
						day_my_date,
						day_date,
						function() {
							CloseDialog.hide();
							SaveViaAjax(CloseDayEditor);
						},
						CloseDayEditor
					);
				} else {
					CloseDayEditor();
				}
			}
		);

		$('.day-form').submit(
			function(event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);

		var detector = new MobileDetect(navigator.userAgent);
		if (detector.mobile()) {
			$('a[href="#mobile"]').tab('show');
		}
	}
);
