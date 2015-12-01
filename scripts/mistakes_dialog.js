var MistakesDialog = {};

$(document).ready(
	function() {
		var mistakes_dialog = $('.custom-spellings-clean-dialog');
		var ok_button = $('.ok-button', mistakes_dialog);

		MistakesDialog = {
			show: function(ok_button_handler) {
				ok_button.off('click');
				ok_button.click(ok_button_handler);

				mistakes_dialog.modal('show');
			},
			hide: function() {
				mistakes_dialog.modal('hide');
			}
		};
	}
);
