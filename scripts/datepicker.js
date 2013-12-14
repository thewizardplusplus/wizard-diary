$(document).ready(function() {
	$('#ParametersForm_start_date').datepicker({
		dateFormat: 'dd.mm.yy',
		showOn: 'button',
		showButtonPanel: true
	});
	$('.ui-datepicker-trigger').hide();
	$('.datapicker-show-button').click(function() {
		$('#ParametersForm_start_date').datepicker('show');
		return false;
	});
});
