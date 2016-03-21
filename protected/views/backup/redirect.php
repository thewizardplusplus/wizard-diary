<?php
	/**
	 * @var BackupController $this
	 * @var string|null $code
	 * @var string|null $error
	 * @var string|null $error_description
	 */
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "utf-8" />

		<script>
			if (window.opener) {
				<?php if (!empty($error)) { ?>
					<?php if ($error != 'access_denied') { ?>
						opener.Backup.error(
							<?php if (!empty($error_description)) { ?>
								'<?= CJavaScript::quote($error_description) ?>'
							<?php } else { ?>
								'неизвестная ошибка'
							<?php } ?>
							+ ' (<?= CJavaScript::quote($error) ?>)'
						);
					<?php } ?>
				<?php } else if (!empty($code)) { ?>
					opener.Backup.create('<?= CJavaScript::quote($code) ?>');
				<?php } ?>
			}

			close();
		</script>

		<title>Перенаправление...</title>
	</head>
	<body></body>
</html>
