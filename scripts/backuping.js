$(document).ready(
	function() {
		var create_backup_button = $('.create-backup-button');
		var app_key = create_backup_button.data('dropbox-app-key');
		var redirect_url = create_backup_button.data('dropbox-redirect-url');
		create_backup_button.click(
			function() {
				var url = 'https://www.dropbox.com/1/oauth2/authorize'
					+ '?response_type=code'
					+ '&client_id=' + app_key
					+ '&redirect_uri=' + encodeURIComponent(
						location.protocol + '//' + location.host + redirect_url
					)
					+ '&force_reapprove=true';
				open(url, '_blank', 'width=640, height=480');
			}
		);

		window.Backup = {
			create: function(authorization_code) {
				var processing_animation_image = $('img', create_backup_button);
				var backup_icon = $('span', create_backup_button);

				create_backup_button.prop('disabled', true);
				processing_animation_image.show();
				backup_icon.hide();

				var backup_url = create_backup_button.data('create-backup-url');
				var data = $.extend(
					{authorization_code: authorization_code},
					CSRF_TOKEN
				);
				var FinishAnimation = function() {
					create_backup_button.prop('disabled', false);
					processing_animation_image.hide();
					backup_icon.show();
				};

				var backup_list = $('#backup-list');
				if (backup_list.length) {
					backup_list.yiiGridView(
						'update',
						{
							type: 'POST',
							url: backup_url,
							data: data,
							success: function() {
								FinishAnimation();
								backup_list.yiiGridView(
									'update',
									{
										url:
											location.pathname
												+ location.search
												+ location.hash
									}
								);
							}
						}
					);
				} else {
					$.post(
						'sdfg' + backup_url,
						data,
						FinishAnimation
					).fail(
						function(xhr, text_status) {
							FinishAnimation();
							AjaxErrorDialog.handler(xhr, text_status);
						}
					);
				}
			},
			error: AjaxErrorDialog.show
		};
	}
);
