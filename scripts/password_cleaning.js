$(document).ready(
	function() {
		var PASSWORD_CLEANING_DELAY_IN_S = 500;

		setTimeout(
			function() {
				$('#ParametersForm_password').val('');
				$('#ParametersForm_password_copy').val('');
			},
			PASSWORD_CLEANING_DELAY_IN_S
		);
	}
);
