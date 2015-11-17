$(document).ready(
	function() {
		var day_editor = $('#DailyPointForm_day');
		day_editor.spinner();

		var year_editor = $('#DailyPointForm_year');
		year_editor.spinner(
			{
				change: function(event, ui) {
					var day_top_limit = DAYS_IN_MY_YEAR;
					if (year_editor.spinner('value') == MY_DATE.year) {
						day_top_limit = MY_DATE.day;
					}

					if (day_editor.spinner('value') > day_top_limit) {
						day_editor.spinner('value', day_top_limit);
					}
					day_editor.spinner('option', 'max', day_top_limit);
				}
			}
		);

		var daily_point_form = $('.daily-point-form');
		$('.add-daily-points-button').click(
			function() {
				DailyPointsDialog.show(
					function() {
						daily_point_form.submit();
					}
				);

				return false;
			}
		);
	}
);
