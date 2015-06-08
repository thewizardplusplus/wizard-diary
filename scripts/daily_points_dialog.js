var DailyPointsDialog = {};

$(document).ready(
	function() {
		var daily_points_dialog = $('.daily-points-dialog');
		var ok_button = $('.ok-button', daily_points_dialog);

		DailyPointsDialog = {
			show: function(ok_button_handler) {
				ok_button.off('click');
				ok_button.click(ok_button_handler);

				daily_points_dialog.modal('show');
			},
			hide: function() {
				daily_points_dialog.modal('hide');
			}
		};
	}
);
