$(document).ready(
	function() {
		var ENTER_KEY_CODE = 13;
		var RequestAnimationFrame =
			requestAnimationFrame
			|| mozRequestAnimationFrame
			|| webkitRequestAnimationFrame
			|| function(callback) {
				setTimeout(callback, 1000 / 60);
			};
		var login_form = $('#login-form');
		var WaitVerification = function() {
			if ($('#codecha_code_submit_button').length) {
				RequestAnimationFrame(WaitVerification);
			} else {
				login_form.submit();
			}
		};
		var SubmitCode = function() {
			var codecha_submit_button = $('#codecha_code_submit_button');
			if (codecha_submit_button.length) {
				codecha_submit_button.click();
				WaitVerification();
			}
		};

		$('#codecha_bottom #codecha_change_challenge').remove();
		$('#codecha_bottom #codecha_language_selector').remove();

		$('.login-button').click(
			function() {
				SubmitCode();
				return false;
			}
		);
		login_form.on(
			'keypress',
			function(event) {
				var key_code = event.keyCode || event.which;
				if (key_code == ENTER_KEY_CODE) {
					var source = event.target || event.srcElement;
					if (source != $('#codecha_code_area').get(0)) {
						event.preventDefault();
					} else if (event.ctrlKey) {
						SubmitCode();
					}
				}
			}
		);
	}
);
