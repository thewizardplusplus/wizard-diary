function stateChoising(url, state) {
	$('#point_list').yiiGridView('update', {
		type: 'POST',
		url: url,
		data: { 'Point[state]': state },
		success: function(data) {
			$('#point_list').yiiGridView('update');
		}
	});

	return false;
}
