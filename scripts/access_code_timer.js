$(document).ready(
	function() {
		var login_url = $('#access-code-lifetime').data('login-url');

		var counting = new countUp(
			'access-code-lifetime',
			ACCESS_CODE_LIFETIME,
			0,
			0,
			ACCESS_CODE_LIFETIME,
			{useEasing: false}
		);
		counting.start(
			function() {
				window.location.replace(login_url);
			}
		);
	}
);
