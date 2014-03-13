var DataPicker = {};
$(document).ready(function() {
	var datepicker_element = $('#ParametersForm_start_date');
	var datepicker_visible = false;
	var interval = setInterval(function() {
		var trigger = $('.ui-datepicker-trigger');
		if (trigger.length) {
			trigger.remove();
			clearInterval(interval);
		}
	}, 0);

	DataPicker = {
		onShow: function() {
			datepicker_visible = true;
		},
		onHide: function() {
			datepicker_visible = false;
		}
	};
	$('.datapicker-show-button').mousedown(function() {
		datepicker_element.datepicker(!datepicker_visible ? 'show' : 'hide');
		return false;
	});
});
