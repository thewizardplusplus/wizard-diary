$(document).ready(function() {
	function addPoint(url) {
		var text = $('#Point_text').val();
		$('#Point_text').val('');

		$('#point_list').yiiGridView('update', {
			type: 'POST',
			url: url,
			data: {
				'Point[text]': text,
				'Point[state]': text != '' ? 'SATISFIED' : 'INITIAL'
			},
			success: function(data) {
				$('#point_list').yiiGridView('update');
			}
		});
	}

	$('.add-point-button').click(function() {
		addPoint($(this).attr('href'));
		return false;
	});

	$('#point-addition-form').submit(function() {
		addPoint($('.add-point-button').attr('href'));
		return false;
	});
});
