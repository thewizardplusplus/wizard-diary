var SpellingsCleaningDialog = {};

$(document).ready(
	function() {
		var spellings_dialog = $('.spellings-cleaning-dialog');
		var ok_button = $('.ok-button', spellings_dialog);

		SpellingsCleaningDialog = {
			show: function(ok_button_handler) {
				ok_button.off('click');
				ok_button.click(ok_button_handler);

				spellings_dialog.modal('show');
			},
			hide: function() {
				spellings_dialog.modal('hide');
			}
		};
	}
);
