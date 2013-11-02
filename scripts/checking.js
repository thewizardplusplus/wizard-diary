function checking(url, checked) {
	$('#point_list').yiiGridView('update', {
		type: 'POST',
		url: url,
		data: { 'Point[check]': checked ? 1 : 0 },
		success: function(data) {
			$('#point_list').yiiGridView('update');
		}
	});

	return false;
}
