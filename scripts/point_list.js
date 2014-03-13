var PointList = {};
$(document).ready(function() {
	var point_list = $('#point-list');
	var add_point_input = $('#Point_text');
	var add_point_button = $('.add-point-button');
	var add_point_url = add_point_button.attr('href');
	var pointListUpdate = function(url, data) {
		point_list.yiiGridView(
			'update',
			{
				type: 'POST',
				url: url,
				data: data,
				success: function() {
					point_list.yiiGridView('update');
				}
			}
		);
	};
	var addPoint = function() {
		var text = add_point_input.val();
		add_point_input.val('');
		pointListUpdate(
			add_point_url,
			{
				'Point[text]': text,
				'Point[state]': text != '' ? 'SATISFIED' : 'INITIAL'
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
				var block = $(this).find('.input-group');
				var submit_button = $(
					'<a class = "input-group-addon" href = "#">'
						+ '<span class = "glyphicon glyphicon-floppy-disk">'
						+ '</span>'
						+ '</a>'
				);
				var cancel_button = $(
					'<a class = "input-group-addon" href = "#">'
						+ '<span class = "glyphicon glyphicon-remove"></span>'
						+ '</a>'
				);

				block.append(submit_button).append(cancel_button);
				submit_button.click(function() {
					if (submit_button.attr('type') != 'submit') {
						form.submit();
					}

					return false;
				});
				cancel_button.click(function() {
					var types = $.editable.types;
					var reset = types[settings.type].reset;

					if (!$.isFunction(reset)) {
						reset = types['defaults'].reset;
					}
					reset.apply(form, [settings, original]);

					return false;
				});
			}
		}
	);
	add_point_button.click(function() {
		addPoint();
		return false;
	});
	$('#point-addition-form').submit(function() {
		addPoint();
		return false;
	});

	PointList = {
		checking: function(url, checked) {
			pointListUpdate(url, { 'Point[check]': checked ? 1 : 0 });
			return false;
		},
		stateChoising: function(url, state) {
			pointListUpdate(url, { 'Point[state]': state });
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
			pointListUpdate(url, { 'Point[order]': $.url(url).param('order') });
			return false;
		},
		deleting: function(link) {
			var url = $(link).attr('href');
			var dialog = $('.modal');
			var description = $('.point-description', dialog);
			var text = $('#point-text-' + $.url(url).param('_id')).text();

			if (text != '') {
				description.html(
					'пункт <strong>&laquo;'
					+ text
					+ '&raquo;</strong>'
				);
			} else {
				description.text('пункт-разделитель');
			}
			$('.ok-button', dialog).click(function() {
				dialog.modal('hide');
				pointListUpdate(url, {});
			});
			dialog.modal('show');

			return false;
		},
		initialize: function() {
			$('.point-text').each(function(id, item) {
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
						callback: function() {
							$('#point_list').yiiGridView('update');
						}
					}
				);
			});
			$('.dropdown-menu a[class^=state]').click(function() {
				var link = $(this);
				pointListUpdate(
					link.parent().parent().data('update-url'),
					{ 'Point[state]': link.data('state') }
				);
			});
		}
	};
	PointList.initialize();
});
