var DeletingDialog = {};

$(document).ready(
	function() {
		var deleting_dialog = $('.deleting-dialog');
		var point_description = $('.point-description', deleting_dialog);
		var ok_button = $('.ok-button', deleting_dialog);

		DeletingDialog = {
			show: function(message, ok_button_handler) {
				point_description.html(message);

				ok_button.off('click');
				ok_button.click(ok_button_handler);

				deleting_dialog.modal('show');
			},
			hide: function() {
				deleting_dialog.modal('hide');
			}
		};
	}
);
