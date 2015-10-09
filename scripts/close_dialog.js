var CloseDialog = {};

$(document).ready(
	function() {
		var close_dialog = $('.close-dialog');
		var day_date = $('.day-date', close_dialog);
		var save_button = $('.save-button', close_dialog);
		var close_button = $('.close-button', close_dialog);

		CloseDialog = {
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

				close_dialog.modal('show');
			},
			hide: function() {
				close_dialog.modal('hide');
			}
		};
	}
);
