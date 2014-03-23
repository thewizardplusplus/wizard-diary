var PointList = {};

$(document).ready(
	function() {
		var point_list = $('#point-list');
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
		var RequestToPointList = function(url, data) {
			point_list.yiiGridView(
				'update',
				{
					type: 'POST',
					url: url,
					data: data,
					success: UpdatePointList
				}
			);
		};

		var add_point_input = $('#Point_text');
		var add_point_button = $('.add-point-button');
		var AddPoint = function() {
			var text = add_point_input.val();
			add_point_input.val('');
			RequestToPointList(
				add_point_button.attr('href'),
				{
					'Point[text]': text,
					'Point[state]': text != '' ? 'SATISFIED' : 'INITIAL'
				}
			);
		};
		add_point_button.click(
			function() {
				AddPoint();
				return false;
			}
		);
		$('#point-addition-form').submit(
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

		PointList = {
			checking: function(url, checked) {
				RequestToPointList(url, { 'Point[check]': checked ? 1 : 0 });
				return false;
			},
			stateChoising: function(url, state) {
				RequestToPointList(url, { 'Point[state]': state });
				return false;
			},
			editing: function(link) {
				var element_id =
					'point-text-'
					+ $.url($(link).attr('href')).param('_id');
				$('#' + element_id).trigger(element_id + '-edit');

				return false;
			},
			move: function(url) {
				RequestToPointList(
					url,
					{ 'Point[order]': $.url(url).param('order') }
				);
				return false;
			},
			deleting: function(link) {
				var url = $(link).attr('href');
				var text = $('#point-text-' + $.url(url).param('_id')).text();
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
				$('.point-text').each(
					function(id, item) {
						item = $(item);
						item.editable(
							item.data('update-url'),
							{
								type: 'bootstrapped-line-edit',
								event: item.attr('id') + '-edit',
								name: 'Point[text]',
								submitdata: { 'ajax': 'updating' },
								onblur: 'ignore',
								indicator:
									'<img src = "'
										+ item.data('saving-icon-url')
										+ '" alt = "Сохранение..." />',
								placeholder: '',
								callback: UpdatePointList
							}
						);
					}
				);
				$('.dropdown-menu a[class^=state]').click(
					function() {
						var link = $(this);
						RequestToPointList(
							link.parent().parent().data('update-url'),
							{ 'Point[state]': link.data('state') }
						);
					}
				);
			}
		};

		PointList.initialize();
	}
);
