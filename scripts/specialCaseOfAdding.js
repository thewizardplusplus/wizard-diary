$(document).ready(function() {
	$('#point-form .special-case').click(function() {
		$('#Point_state').val('SATISFIED');
		$('#point-form').submit();
	});
});
