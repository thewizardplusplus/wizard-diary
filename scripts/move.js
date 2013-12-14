function move(url) {
	$('#point_list').yiiGridView('update', {
		type: 'POST',
		url: url,
		data: { 'Point[order]': parseInt($.url(url).param('order')) },
		success: function(data) {
			$('#point_list').yiiGridView('update');
		}
	});

	return false;
}
