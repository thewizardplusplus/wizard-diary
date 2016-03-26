var AjaxErrorDialog = {};

$(document).ready(
	function() {
		var ajax_error_dialog = $('.ajax-error-dialog');
		var error_description = $('.error-description', ajax_error_dialog);

		AjaxErrorDialog = {
			show: function(message) {
				error_description.html('&laquo;' + message + '&raquo;');
				ajax_error_dialog.modal('show');
			},
			handler: function(xhr, text_status) {
				var message = 'неизвестная ошибка';
				switch (text_status) {
					case 'timeout':
						message = 'превышено время запроса';
						break;
					case 'parsererror':
						message = 'ошибка парсинга';
						break;
					default:
						if (xhr.status && !/^\s*$/.test(xhr.status)) {
							message = 'ошибка ' + xhr.status;
						}

						break;
				}

				AjaxErrorDialog.show(message);
			}
		};
	}
);
