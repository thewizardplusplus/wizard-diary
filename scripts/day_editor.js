$(document).ready(
	function() {
		var lang_tools = ace.require('ace/ext/language_tools');

		var day_editor = ace.edit('day-editor');
		day_editor.$blockScrolling = Infinity;
		day_editor.setTheme('ace/theme/twilight');
		day_editor.setShowInvisibles(true);
		day_editor.setShowPrintMargin(false);

		day_editor.setOptions(
			{
				enableBasicAutocompletion: true,
				enableLiveAutocompletion: true
			}
		);
		lang_tools.addCompleter(
			{
				getCompletions: function(
					editor,
					session,
					position,
					prefix,
					callback
				) {
					var line_prefix =
						session
						.getLine(position.row)
						.substr(0, position.column);
					var line_prefix_parts =
						line_prefix
						.split(',')
						.map(
							function(part) {
								return part.trim();
							}
						);

					var alternatives = [];
					switch (line_prefix_parts.length) {
						case 1:
							alternatives = Object.keys(
								POINT_HIERARCHY.hierarchy
							);

							break;
						case 2:
							var level_1 = line_prefix_parts[0];
							if (
								POINT_HIERARCHY
									.hierarchy
									.hasOwnProperty(level_1)
							) {
								alternatives =
									POINT_HIERARCHY
									.hierarchy[level_1];
							}

							break;
						default:
							if (
								POINT_HIERARCHY
									.hierarchy
									.hasOwnProperty(line_prefix_parts[0])
								&& $.inArray(
									line_prefix_parts[1],
									POINT_HIERARCHY
										.hierarchy[line_prefix_parts[0]]
								) !== -1
							) {
								var tails = Object.keys(POINT_HIERARCHY.tails);
								for (var i = 0; i < tails.length; i++) {
									var tail = tails[i];
									var counter = POINT_HIERARCHY.tails[tail];
									alternatives.push(
										{
											value: tail,
											score: counter
										}
									);
								}
							}
					}
					alternatives = alternatives.map(
						function(alternative) {
							if (typeof alternative === 'string') {
								alternative = {value: alternative};
							}
							alternative.meta = 'global';

							return alternative;
						}
					);

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
