// setting callback for initialization jQueryFormStyler's checkbox
$(document).ready(function() {
	$('#LoginForm_remember_me').styler();
	// addition styles to jQueryFormStyler's checkbox for display Bootstrap's
	// icon
	$('.jq-checkbox div').addClass('glyphicon glyphicon-ok');
});
