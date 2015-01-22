$(document).ready(
	function() {
		var UpdateAccessInfo = function() {
			var start_time = Date.now();

			var get_info_url =
				$('.access-totally-info-view')
				.data('get-info-url');
			$.get(
				get_info_url,
				function(info) {
					$('.access-counter-view').text(info.counter);
					$('.access-speed-by-day-view').text(info.speed.by_day);
					$('.access-speed-by-hour-view').text(info.speed.by_hour);
					$('.access-speed-by-minute-view').text(
						info.speed.by_minute
					);

					var rest_time =
						ACCESS_INFO_UPDATE_PAUSE_IN_S * 1000
						- (Date.now() - start_time);
					setTimeout(UpdateAccessInfo, rest_time > 0 ? rest_time : 0);
				},
				'json'
			).fail(AjaxErrorDialog.handler);
		};

		UpdateAccessInfo();
	}
);
