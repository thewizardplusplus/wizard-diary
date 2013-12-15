$(document).ready(function() {
	$('.create-backup-button').click(function() {
		$('#backup-list').yiiGridView('update', {
			type: 'POST',
			url: $(this).data('create-backup-url'),
			success: function(data) {
				$('#backup-list').yiiGridView('update');
			}
		});
	});
});
