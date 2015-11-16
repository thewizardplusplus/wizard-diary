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

		var add_daily_points_button = $('.add-daily-points-button');
		var add_daily_points_url = add_daily_points_button.data(
			'add-daily-points-url'
		);
		add_daily_points_button.click(
			function() {
				DailyPointsDialog.show(
					function() {
						console.log('ok');
					}
				);
			}
		);

		$('.daily-point-form').submit(
			function(event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);
	}
);
