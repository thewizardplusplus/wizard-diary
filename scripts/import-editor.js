$(document).ready(
	function() {
		var import_editor = ace.edit('import-editor');
		import_editor.setTheme('ace/theme/twilight');
		import_editor.setShowInvisibles(true);
		import_editor.setShowPrintMargin(false);

		var form = $('.import-form');
		form.submit(
			function() {
				var points_description = import_editor.getValue();
				$('#Import_points_description').val(points_description);
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
				{'Import[points_description]': import_editor.getValue()},
				CSRF_TOKEN
			);
			$.post(save_url, data, FinishAnimation).fail(
				function(xhr, text_status) {
					FinishAnimation();
					AjaxErrorDialog.handler(xhr, text_status);
				}
			);
		};

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

		save_button.click(SaveViaAjax);
		$('.save-and-import-button').click(
			function() {
				$('#Import_import').val('true');
				form.submit();
			}
		);
	}
);
