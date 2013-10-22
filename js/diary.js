function processPointStateChoise(id, state) {
	jQuery('#point_list').yiiGridView('update', {
		type: 'POST',
		url: '?r=point/update&id=' + id,
		data: {
			'Point[state]': state,
			'Point[check]': state != 'INITIAL' ? 1 : 0
		},
		success: function(data) {
			jQuery('#point_list').yiiGridView('update');
		}
	});

	return false;
}

function processPointChecked(input) {
	var input = jQuery(input);
	var checked = input.attr('checked');

	jQuery('#point_list').yiiGridView('update', {
		type: 'POST',
		url: '?r=point/update&id=' + input.val(),
		data: {
			'Point[check]': typeof checked !== 'undefined' && checked !== false
				? 1 : 0
		},
		success: function(data) {
			jQuery('#point_list').yiiGridView('update');
		}
	});

	return false;
}
