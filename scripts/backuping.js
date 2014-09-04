$(document).ready(
	function() {
		$('.create-backup-button').click(
			function() {
				open(
					'https://www.dropbox.com/1/oauth2/authorize?'
						+ 'response_type=code'
						+ '&client_id=' + DROPBOX_APP_KEY
						+ '&redirect_uri='
						+ encodeURIComponent(
							location.protocol
								+ '//'
								+ location.host
								+ DROPBOX_REDIRECT_URL
							)
						+ '&state=' + CSRF_TOKEN[CSRF_TOKEN_NAME],
					'_blank',
					'width=640, height=480'
				);
			}
		);
	}
);
