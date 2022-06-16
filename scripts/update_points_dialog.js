var UpdatePointsDialog = {};

$(document).ready(
	function() {
		var update_points_dialog = $('.update-points-dialog');
		var ok_button = $('.ok-button', update_points_dialog);

		UpdatePointsDialog = {
			show: function(ok_button_handler) {
				ok_button.off('click');
				ok_button.click(ok_button_handler);

				update_points_dialog.modal('show');
			},
			hide: function() {
				update_points_dialog.modal('hide');
			}
		};
	}
);
