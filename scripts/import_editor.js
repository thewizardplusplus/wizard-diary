$(document).ready(
	function() {
		var IMPORT_EDITOR_SAVE_TIMEOUT = 2000;

		var import_editor = ace.edit('import-editor');
		import_editor.setTheme('ace/theme/twilight');
		import_editor.setShowInvisibles(true);
		import_editor.setShowPrintMargin(false);

		var FormatPoints = function(points, cursor_position) {
			points = points.map(
				function(point, index) {
					if (
						typeof cursor_position == 'undefined'
						|| cursor_position.row != index
					) {
						return point.replace(/\s+$/, '');
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
			points_description = result.points.join('\n');

			return {
				points_description: points_description,
				cursor_position: result.cursor_position
			};
		};
		import_editor.formatAndReturnPointsDescription = function() {
			var points_description = import_editor.getValue();
			var cursor_position = import_editor.getCursorPosition();
			var result = FormatPointsDescription(
				points_description,
				cursor_position
			);

			import_editor.setValue(result.points_description, -1);
			if (typeof result.cursor_position != 'undefined') {
				import_editor.moveCursorToPosition(result.cursor_position);
			}

			return result.points_description;
		};

		var saved_flag_container = $('.saved-flag');
		var saved_flag_icon = $('span', saved_flag_container);
		var SetSavedFlag = function(saved) {
			if (saved) {
				saved_flag_container
					.addClass('label-success')
					.removeClass('label-danger');
				saved_flag_icon
					.addClass('glyphicon-floppy-saved')
					.removeClass('glyphicon-floppy-remove');
			} else {
				saved_flag_container
					.addClass('label-danger')
					.removeClass('label-success');
				saved_flag_icon
					.addClass('glyphicon-floppy-remove')
					.removeClass('glyphicon-floppy-saved');
			}
		};

		var number_of_points_view = $('.number-of-points-view');
		var FormatNumberOfPoints = function(number_of_points) {
			var unit = '';
			var modulo = number_of_points % 10;
			if (
				modulo == 1
				&& (number_of_points < 10 || number_of_points > 20)
			) {
				unit = 'пункт';
			} else if (
				modulo > 1 && modulo < 5
				&& (number_of_points < 10 || number_of_points > 20)
			) {
				unit = 'пункта';
			} else {
				unit = 'пунктов';
			}

			return number_of_points.toString() + ' ' + unit;
		};
		var SetNumberOfPoints = function() {
			var points_description = import_editor.getValue();
			var points = points_description.split('\n');
			points = FormatPoints(points);

			var number_of_points = points.length - 1;
			if (number_of_points < 0) {
				number_of_points = 0;
			}

			var formatted_number_of_points = FormatNumberOfPoints(
				number_of_points
			);
			number_of_points_view.text(formatted_number_of_points);
		};

		import_editor.on(
			'change',
			function() {
				SetSavedFlag(false);
				SetNumberOfPoints();
			}
		);
		import_editor.on(
			'paste',
			function(event) {
				event.text =
					event.text
					.replace(/\u21e5|\u00b7/g, ' ')
					.replace('\u00b6', '');
			}
		);

		var save_button = $('.save-import-button');
		var save_url = save_button.data('save-url');
		var processing_animation_image = $('img', save_button);
		var save_icon = $('span', save_button);
		var import_editor_container = $(import_editor.container);
		var FinishAnimation = function() {
			save_button.prop('disabled', false);
			processing_animation_image.hide();
			save_icon.show();
			import_editor_container.removeClass('wait');
		};
		var SaveViaAjax = function() {
			save_button.prop('disabled', true);
			processing_animation_image.show();
			save_icon.hide();
			import_editor_container.addClass('wait');

			var data = $.extend(
				{
					'Import[points_description]':
						import_editor
						.formatAndReturnPointsDescription()
				},
				CSRF_TOKEN
			);
			$.post(
				save_url,
				data,
				function() {
					SetSavedFlag(true);
					FinishAnimation();
				}
			).fail(
				function(xhr, text_status) {
					FinishAnimation();
					AjaxErrorDialog.handler(xhr, text_status);
				}
			);
		};

		var form = $('.import-form');
		form.submit(
			function() {
				var points_description =
					import_editor
					.formatAndReturnPointsDescription();
				$('#Import_points_description').val(points_description);
			}
		);

		save_button.click(SaveViaAjax);

		var save_and_import_button = $('.save-and-import-button');
		var import_date = save_and_import_button.data('date');
		var import_my_date = save_and_import_button.data('my-date');
		save_and_import_button.click(
			function() {
				ImportDialog.show(
					import_my_date,
					import_date,
					function() {
						$('#Import_import').val('true');
						form.submit();
					}
				);
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
	}
);
