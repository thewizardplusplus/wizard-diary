$(document).ready(
	function() {
		var resend_access_code_button = $('.resend-access-code-button');
		var resend_access_code_url = resend_access_code_button.data(
			'resend-access-code-url'
		);
		var processing_animation_image = $('img', resend_access_code_button);
		var icon_and_text = $('span', resend_access_code_button);
		var FinishAnimation = function() {
			resend_access_code_button.prop('disabled', false);
			processing_animation_image.hide();
			icon_and_text.show();
		};

		resend_access_code_button.click(
			function() {
				resend_access_code_button.prop('disabled', true);
				processing_animation_image.show();
				icon_and_text.hide();

				$.post(
					resend_access_code_url,
					CSRF_TOKEN,
					FinishAnimation
				).fail(AjaxErrorDialog.handler);
			}
		);
	}
);
