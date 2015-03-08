$(document).ready(
	function() {
		var IMPORT_EDITOR_SAVE_TIMEOUT = 2000;

		var import_editor = ace.edit('import-editor');
		import_editor.setTheme('ace/theme/twilight');
		import_editor.setShowInvisibles(true);
		import_editor.setShowPrintMargin(false);

		var FormatPoints = function(points) {
			points = points.map(
				function(point) {
					return point.replace(/\s+$/, '');
				}
			);

			while (points.length && points[0].trim().length == 0) {
				points.shift();
			}
			while (points.length && points.slice(-1)[0].trim().length == 0) {
				points.pop();
			}
			points.push('');

			return points;
		};
		var FormatPointsDescription = function(points_description) {
			var points = points_description.split('\n');
			points = FormatPoints(points);

			return points.join('\n');
		};
		import_editor.formatAndReturnPointsDescription = function() {
			var points_description = FormatPointsDescription(
				import_editor.getValue()
			);

			var cursor_position = import_editor.getCursorPosition();
			import_editor.setValue(points_description, -1);
			import_editor.moveCursorToPosition(cursor_position);

			return points_description;
		};

		var save_timer = null;
		import_editor.on(
			'change',
			function() {
				if (import_editor.curOp && import_editor.curOp.command.name) {
					clearTimeout(save_timer);
					save_timer = setTimeout(
						SaveViaAjax,
						IMPORT_EDITOR_SAVE_TIMEOUT
					);
				}
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
			$.post(save_url, data, FinishAnimation).fail(
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
		save_and_import_button.click(
			function() {
				ImportDialog.show(
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
