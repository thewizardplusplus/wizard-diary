$(document).ready(
	function() {
		var add_daily_points_button = $('.add-daily-points-button');
		var add_daily_points_url = add_daily_points_button.data(
			'add-daily-points-url'
		);
		add_daily_points_button.click(
			function() {
				DailyPointsDialog.show(
					function() {
						window.location = add_daily_points_url;
					}
				);
			}
		);
	}
);
