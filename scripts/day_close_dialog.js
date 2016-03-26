var DayCloseDialog = {};

$(document).ready(
	function() {
		var day_close_dialog = $('.day-close-dialog');
		var day_date = $('.day-date', day_close_dialog);
		var save_button = $('.save-button', day_close_dialog);
		var close_button = $('.close-button', day_close_dialog);

		DayCloseDialog = {
			show: function(
				my_date,
				date,
				save_button_handler,
				close_button_handler
			) {
				day_date.text(my_date);
				day_date.attr('title', date);

				save_button.off('click');
				save_button.click(save_button_handler);

				close_button.off('click');
				close_button.click(close_button_handler);

				day_close_dialog.modal('show');
			},
			hide: function() {
				day_close_dialog.modal('hide');
			}
		};
	}
);
