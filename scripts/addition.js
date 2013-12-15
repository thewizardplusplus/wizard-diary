$(document).ready(function() {
	$('.add-point-button').click(function() {
		var text = $('#Point_text').val();
		$('#point_list').yiiGridView('update', {
			type: 'POST',
			url: $(this).attr('href'),
			data: {
				'Point[text]': text,
				'Point[state]': text != '' ? 'SATISFIED' : 'INITIAL'
			},
			success: function(data) {
				$('#point_list').yiiGridView('update');
			}
		});

		return false;
	});
});
