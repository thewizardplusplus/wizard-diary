function deleting(link) {
	var url = $(link).attr('href');
	var point_id = $.url(url).param('_id');
	var element_id = 'point-text-' + point_id;

	$('.modal .point-text').text($('#' + element_id).text());
	$('.modal .ok-button').click(function() {
		$('.modal').modal('hide');
		$('#point_list').yiiGridView('update', {
			type: 'POST',
			url: url,
			success: function(data) {
				$('#point_list').yiiGridView('update');
			}
		});
	});
	$('.modal').modal('show');

	return false;
}
