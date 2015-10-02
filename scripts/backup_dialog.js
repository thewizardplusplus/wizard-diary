var BackupDialog = {};

$(document).ready(
	function() {
		var backup_dialog = $('.backup-dialog');
		var ok_button = $('.ok-button', backup_dialog);

		BackupDialog = {
			show: function(ok_button_handler) {
				ok_button.off('click');
				ok_button.click(ok_button_handler);

				backup_dialog.modal('show');
			},
			hide: function() {
				backup_dialog.modal('hide');
			}
		};
	}
);
