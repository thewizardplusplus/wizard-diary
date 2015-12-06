var DailyPointCloseDialog = {};

$(document).ready(
	function() {
		var daily_point_close_dialog = $('.daily-point-close-dialog');
		var save_button = $('.save-button', daily_point_close_dialog);
		var close_button = $('.close-button', daily_point_close_dialog);

		DailyPointCloseDialog = {
			show: function(save_button_handler, close_button_handler) {
				save_button.off('click');
				save_button.click(save_button_handler);

				close_button.off('click');
				close_button.click(close_button_handler);

				daily_point_close_dialog.modal('show');
			},
			hide: function() {
				daily_point_close_dialog.modal('hide');
			}
		};
	}
);
