$(document).ready(
	function() {
		var day_editor = ace.edit('day-editor');
		day_editor.setTheme('ace/theme/twilight');
		day_editor.setShowInvisibles(true);
		day_editor.setShowPrintMargin(false);

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
		var SetNumberOfPoints = function() {
			var points_description = day_editor.getValue();
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
				SetNumberOfPoints();
			}
		);

		var save_button = $('.save-day-button');
		var save_url = save_button.data('save-url');
		var processing_animation_image = $('img', save_button);
		var save_icon = $('span', save_button);
		var day_editor_container = $(day_editor.container);
		var FinishAnimation = function() {
			save_button.prop('disabled', false);
			processing_animation_image.hide();
			save_icon.show();
			day_editor_container.removeClass('wait');
		};
		var SaveViaAjax = function(callback) {
			save_button.prop('disabled', true);
			processing_animation_image.show();
			save_icon.hide();
			day_editor_container.addClass('wait');

			var data = $.extend(
				{'points_description': day_editor.getValue()},
				CSRF_TOKEN
			);
			$.post(
				save_url,
				data,
				function() {
					SetSavedFlag(true);
					FinishAnimation();

					if (callback) {
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
		save_button.click(SaveViaAjax);
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

		var form = $('.day-form');
		form.submit(
			function() {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);
	}
);
