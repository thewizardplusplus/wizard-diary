$(document).ready(
	function() {
		var ACCESS_INFO_UPDATE_PAUSE_IN_S = 1;

		var UpdateAccessInfo = function() {
			var start_time = Date.now();

			var test_value = Math.random();
			$('.access-counter-view').text(test_value);
			$('.access-speed-by-day-view').text(test_value);
			$('.access-speed-by-hour-view').text(test_value);
			$('.access-speed-by-minute-view').text(test_value);

			var rest_time =
				ACCESS_INFO_UPDATE_PAUSE_IN_S * 1000
				- (Date.now() - start_time);
			console.log(rest_time);
			setTimeout(UpdateAccessInfo, rest_time > 0 ? rest_time : 0);
		};

		UpdateAccessInfo();
	}
);
