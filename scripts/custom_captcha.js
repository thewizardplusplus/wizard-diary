$(document).ready(
	function() {
		var verify_code_image = $('#LoginForm_verify_code_image');
		verify_code_image.siblings('button').click(
			function() {
				verify_code_image.click();
			}
		);
	}
);
