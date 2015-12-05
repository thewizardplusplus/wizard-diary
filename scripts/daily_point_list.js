var DailyPointList = {};

$(document).ready(
	function() {
		var point_list = $('#daily-point-list');
		var UpdatePointList = function() {
			point_list.yiiGridView(
				'update',
				{
					url:
						location.pathname
							+ location.search
							+ location.hash
				}
			);
		};
		var RequestToPointList = function(url, data, callback) {
			$.extend(data, CSRF_TOKEN);
			point_list.yiiGridView(
				'update',
				{
					type: 'POST',
					url: url,
					data: data,
					success: function() {
						UpdatePointList();

						if (typeof callback == 'function') {
							callback();
						}
					}
				}
			);
		};

		$.editable.addInputType(
			'bootstrapped-line-edit',
			{
				element : function() {
					var block = $('<div class = "input-group"></div>');
					var input = $('<input class = "form-control" />');

					$(this).append(block);
					block.append(input);

					return input;
				},
				buttons: function(settings, original) {
					var form = this;
					var block = $(form).find('.input-group');
					var submit_button = $(
						'<a class = "input-group-addon" href = "#">'
							+ '<span class = "glyphicon glyphicon-floppy-disk">'
							+ '</span>'
							+ '</a>'
					);
					var cancel_button = $(
						'<a class = "input-group-addon" href = "#">'
							+ '<span class = "glyphicon glyphicon-remove">'
							+ '</span>'
							+ '</a>'
					);

					block.append(submit_button).append(cancel_button);
					submit_button.click(
						function() {
							if (submit_button.attr('type') != 'submit') {
								form.submit();
							}

							return false;
						}
					);
					cancel_button.click(
						function() {
							var reset = $.editable.types[settings.type].reset;
							if (!$.isFunction(reset)) {
								reset = $.editable.types['defaults'].reset;
							}

							reset.apply(form, [settings, original]);

							return false;
						}
					);
				}
			}
		);

		DailyPointList = {
			editing: function(link) {
				var element_id =
					'daily-point-text-'
						+ $.url($(link).attr('href')).param('_id');
				$('#' + element_id).trigger(element_id + '-edit');

				return false;
			},
			initialize: function() {
				$('.daily-point-text').each(
					function(id, item) {
						item = $(item);
						item.editable(
							item.data('update-url'),
							{
								type: 'bootstrapped-line-edit',
								event: item.attr('id') + '-edit',
								name: 'DailyPoint[text]',
								data: item.data('text'),
								submitdata: CSRF_TOKEN,
								onblur: 'ignore',
								indicator:
									'<img src = "'
										+ item.data('saving-icon-url')
										+ '" alt = "Сохранение..." />',
								placeholder: '',
								callback: UpdatePointList,
								onerror: function(settings, original, xhr) {
									AjaxErrorDialog.handler(xhr);
									original.reset();
								}
							}
						);
					}
				);
			}
		};

		DailyPointList.initialize();
	}
);
