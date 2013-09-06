function processPointStateChoise(id, state) {
	jQuery('#point_list').yiiGridView('update', {
		type: 'POST',
		url: '?r=point/update&id=' + id,
		data: { 'Point[state]': state },
		success: function(data) {
			jQuery('#point_list').yiiGridView('update');
		}
	});

	return false;
}
