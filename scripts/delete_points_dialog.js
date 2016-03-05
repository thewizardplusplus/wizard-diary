var DeletePointsDialog = {};

$(document).ready(
	function() {
		var delete_points_dialog = $('.delete-points-dialog');
		var ok_button = $('.ok-button', delete_points_dialog);

		DeletePointsDialog = {
			show: function(ok_button_handler) {
				ok_button.off('click');
				ok_button.click(ok_button_handler);

				delete_points_dialog.modal('show');
			},
			hide: function() {
				delete_points_dialog.modal('hide');
			}
		};
	}
);
