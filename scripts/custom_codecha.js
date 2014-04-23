$(document).ready(
	function() {
		var RequestAnimationFrame =
			requestAnimationFrame
			|| mozRequestAnimationFrame
			|| webkitRequestAnimationFrame
			|| function(callback) {
				setTimeout(callback, 1000 / 60);
			};
		var WaitVerification = function() {
			if ($('#codecha_code_submit_button').length) {
				RequestAnimationFrame(WaitVerification);
			} else {
				$('.login-button').click();
			}
		};

		$('.login-button').click(
			function() {
				var codecha_submit_button = $('#codecha_code_submit_button');
				if (codecha_submit_button.length) {
					codecha_submit_button.click();
					WaitVerification();

					return false;
				} else {
					return true;
				}
			}
		);
	}
);
