var MistakesCleaningDialog = {};

$(document).ready(
	function() {
		var mistakes_dialog = $('.custom-spellings-cleaning-dialog');
		var ok_button = $('.ok-button', mistakes_dialog);

		MistakesCleaningDialog = {
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
