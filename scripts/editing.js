function initializeEditors() {
	$('.point-text').each(function(id, item) {
		item = $(item);
		item.editable(item.data('update-url'), {
			type: 'bootstrapped-line-edit',
			event: item.attr('id') + '-edit',
			name: 'Point[text]',
			onblur: 'ignore',
			indicator: '<img src = "' + item.data('saving-icon-url') + '" alt ='
				+ ' "Сохранение..." />',
			placeholder: '',
			callback: function() {
				$('#point_list').yiiGridView('update');
			}
		});
	});
}

function editing(link) {
	var url = $(link).attr('href');
	var point_id = $.url(url).param('id');
	var element_id = 'point-text-' + point_id;
	$('#' + element_id).trigger(element_id + '-edit');

	return false;
}

$(document).ready(function() {
	$.editable.addInputType('bootstrapped-line-edit', {
		element : function(settings, original) {
			var block = $('<div class = "input-group"></div>');
			$(this).append(block);

			var input = $('<input class = "form-control" />');
			block.append(input);

			return input;
		},
		buttons: function(settings, original) {
			var form = this;
			var block = $(this).find('.input-group');

			var submit_button = $('<a class = "input-group-addon" href = "#">' +
				'<span class = "glyphicon glyphicon-floppy-disk"></span></a>');
			block.append(submit_button);
			submit_button.click(function() {
				if (submit_button.attr('type') != 'submit') {
					form.submit();
				}

				return false;
			});

			var cancel_button = $('<a class = "input-group-addon" href = "#">' +
				'<span class = "glyphicon glyphicon-remove"></span></a>');
			block.append(cancel_button);
			cancel_button.click(function() {
				if ($.isFunction($.editable.types[settings.type].reset)) {
					var reset = $.editable.types[settings.type].reset;
				} else {
					var reset = $.editable.types['defaults'].reset;
				}
				reset.apply(form, [settings, original]);

				return false;
			});
		}
	});

	initializeEditors();
});
