function process_recaptcha_response(response) {
	$('#LoginForm_verify_code').val(response);
}
