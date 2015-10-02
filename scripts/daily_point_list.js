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

		var add_point_input = $('#DailyPoint_text');
		var add_point_button = $('.add-daily-point-button');
		var AddPoint = function() {
			var text = add_point_input.val();
			add_point_input.val('');
			RequestToPointList(
				add_point_button.attr('href'),
				{ 'DailyPoint[text]': text },
				function() {
					$('html, body').animate(
						{
							scrollTop: $(document).height() - $(window).height()
						}
					);
				}
			);
		};
		add_point_button.click(
			function() {
				AddPoint();
				return false;
			}
		);
		$('#daily-point-addition-form').submit(
			function() {
				AddPoint();
				return false;
			}
		);

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
			move: function(url) {
				RequestToPointList(
					url,
					{ 'DailyPoint[order]': $.url(url).param('order') }
				);
				return false;
			},
			editing: function(link) {
				var element_id =
					'daily-point-text-'
						+ $.url($(link).attr('href')).param('_id');
				$('#' + element_id).trigger(element_id + '-edit');

				return false;
			},
			deleting: function(link) {
				var url = $(link).attr('href');
				var text = $('#daily-point-text-' + $.url(url).param('_id'))
					.data('text');
				if (text != '') {
					text =
						'пункт <strong>&laquo;'
							+ text
							+ '&raquo;</strong>';
				} else {
					text = 'пункт-разделитель';
				}
				DeletingDialog.show(
					text,
					function() {
						DeletingDialog.hide();
						RequestToPointList(url, {});
					}
				);

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
