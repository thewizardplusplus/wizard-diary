var BackupUtils = {};

$(document).ready(
	function() {
		var create_backup_button = $('.create-backup-button');
		var create_backup_url = create_backup_button.data('create-backup-url');
		var save_backup_url = create_backup_button.data('save-backup-url');
		var app_key = create_backup_button.data('dropbox-app-key');
		var redirect_url = create_backup_button.data('dropbox-redirect-url');
		var processing_animation_image = $('img', create_backup_button);
		var backup_icon = $('span', create_backup_button);
		var FinishAnimation = function() {
			create_backup_button.prop('disabled', false);
			processing_animation_image.hide();
			backup_icon.show();
		};
		var GetAccessToDropbox = function() {
			var url = 'https://www.dropbox.com/1/oauth2/authorize'
				+ '?response_type=code'
				+ '&client_id=' + app_key
				+ '&redirect_uri=' + encodeURIComponent(
					location.protocol + '//' + location.host + redirect_url
				)
				+ '&force_reapprove=true';
			open(url, '_blank', 'width=640, height=480');
		};
		BackupUtils.error = function(xhr, text_status) {
			FinishAnimation();
			AjaxErrorDialog.handler(xhr, text_status);
		};

		var backup_path = '';
		window.Backup = {
			create: function(authorization_code) {
				var data = $.extend(
					{
						authorization_code: authorization_code,
						backup_path: backup_path
					},
					CSRF_TOKEN
				);

				$.post(
					save_backup_url,
					data,
					function() {
						FinishAnimation();
					}
				).fail(BackupUtils.error);
			},
			error: AjaxErrorDialog.show
		};

		create_backup_button.click(
			function() {
				create_backup_button.prop('disabled', true);
				processing_animation_image.show();
				backup_icon.hide();

				var backup_list = $('#backup-list');
				if (backup_list.length) {
					backup_list.yiiGridView(
						'update',
						{
							type: 'POST',
							url: create_backup_url,
							data: CSRF_TOKEN,
							success: function(data) {
								backup_list.yiiGridView(
									'update',
									{
										url:
											location.pathname
												+ location.search
												+ location.hash
									}
								);

								data = JSON.parse(data);
								backup_path = data.backup_path;

								GetAccessToDropbox();
							}
						}
					);
				} else {
					$.post(
						create_backup_url,
						CSRF_TOKEN,
						function(data) {
							backup_path = data.backup_path;
							GetAccessToDropbox();
						},
						'json'
					).fail(BackupUtils.error);
				}
			}
		);
	}
);
