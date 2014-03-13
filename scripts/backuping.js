$(document).ready(function() {
	$('.create-backup-button').click(function() {
		var button = $(this);
		var processing_animation = $('img', button);
		var backup_icon = $('span', button);
		var backup_list = $('#backup-list');

		var url = button.data('create-backup-url');
		var finishAnimation = function() {
			button.prop('disabled', false);
			processing_animation.hide();
			backup_icon.show();
		};

		button.prop('disabled', true);
		processing_animation.show();
		backup_icon.hide();

		if (backup_list.length) {
			backup_list.yiiGridView(
				'update',
				{
					type: 'POST',
					url: url,
					success: function() {
						finishAnimation();
						backup_list.yiiGridView('update');
					}
				}
			);
		} else {
			$.post(url, finishAnimation);
		}
	});
});
