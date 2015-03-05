$(document).ready(
	function() {
		var UpdateAccessLog = function() {
			var start_time = Date.now();

			$('#access-list').yiiGridView(
				'update',
				{
					url:
						location.pathname
							+ location.search
							+ location.hash
				}
			);
		};
		var StartAccessLogUpdating = function() {
			setInterval(UpdateAccessLog, ACCESS_INFO_UPDATE_PAUSE_IN_S * 1000);
		};

		setTimeout(
			StartAccessLogUpdating,
			ACCESS_INFO_UPDATE_PAUSE_IN_S * 1000
		);
	}
);
