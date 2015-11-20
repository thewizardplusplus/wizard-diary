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

		$('#ParametersForm_session_lifetime_in_min').spinner();
		$('#ParametersForm_access_log_lifetime_in_s').spinner();
	}
);
