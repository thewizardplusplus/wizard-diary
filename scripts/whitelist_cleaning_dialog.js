var WhitelistCleaningDialog = {};

$(document).ready(
	function() {
		var whitelist_dialog = $('.whitelist-cleaning-dialog');
		var ok_button = $('.ok-button', whitelist_dialog);

		WhitelistCleaningDialog = {
			show: function(ok_button_handler) {
				ok_button.off('click');
				ok_button.click(ok_button_handler);

				whitelist_dialog.modal('show');
			},
			hide: function() {
				whitelist_dialog.modal('hide');
			}
		};
	}
);
