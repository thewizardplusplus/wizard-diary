<?php
	/**
	 * @var CController $this
	 */

	Yii::app()->getClientScript()->registerPackage('jquery');
	Yii::app()->getClientScript()->registerScriptFile(
		'//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js',
		CClientScript::POS_HEAD
	);
	if (!Yii::app()->user->isGuest) {
		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/ajax_error_dialog.js'),
			CClientScript::POS_HEAD
		);
		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/backuping.js'),
			CClientScript::POS_HEAD
		);
	}

	$copyright_years = Constants::COPYRIGHT_START_YEAR;
	$current_year = date('Y');
	if ($current_year > Constants::COPYRIGHT_START_YEAR) {
		$copyright_years .= '-' . $current_year;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "utf-8" />
		<meta name = "viewport" content = "width=device-width" />

		<link
			rel = "icon"
			type = "image/png"
			href = "<?= Yii::app()->request->baseUrl ?>/images/logo.png" />

		<link
			rel = "stylesheet"
			href = "//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
		<link
			rel = "stylesheet"
			href = "<?= Yii::app()->request->baseUrl ?>/styles/diary.css" />

		<title><?= $this->pageTitle ?></title>
	</head>
	<body>
		<nav class = "navbar navbar-default navbar-fixed-top navbar-inverse">
			<section class = "container">
				<div class = "navbar-header">
					<?php if (!Yii::app()->user->isGuest) { ?>
						<button
							class = "navbar-toggle"
							data-toggle = "collapse"
							data-target = "#navbar-collapse">
							<span class = "icon-bar"></span>
							<span class = "icon-bar"></span>
							<span class = "icon-bar"></span>
						</button>
					<?php } ?>
					<a
						class = "navbar-brand"
						href = "<?= Yii::app()->homeUrl ?>">
						<?= Yii::app()->name ?>
					</a>
				</div>

				<?php if (!Yii::app()->user->isGuest) { ?>
					<div
						id = "navbar-collapse"
						class = "collapse navbar-collapse">
						<?php $this->widget(
							'zii.widgets.CMenu',
							array(
								'items' => array(
									array(
										'label' => 'Бекапы',
										'url' => array('backup/list')
									)
								),
								'htmlOptions' => array(
									'class' => 'nav navbar-nav'
								)
							)
						); ?>
						<button
							class = "btn btn-primary navbar-btn navbar-left create-backup-button"
							data-create-backup-url = "<?= $this->createUrl('backup/create') ?>">
							<img
								src = "<?= Yii::app()->request->baseUrl ?>/images/processing-icon.gif"
								alt = "..." />
							<span class = "glyphicon glyphicon-compressed">
							</span>
							<span>Создать бекап</span>
						</button>
						<?php $this->widget(
							'zii.widgets.CMenu',
							array(
								'items' => array(
									array(
										'label' => 'Параметры',
										'url' => array('parameters/update')
									)
								),
								'htmlOptions' => array(
									'class' => 'nav navbar-nav'
								)
							)
						); ?>
						<?= CHtml::beginForm(
							$this->createUrl('site/logout'),
							'post',
							array('class' => 'navbar-form navbar-right')
						) ?>
							<?= CHtml::htmlButton(
								'<span class = "glyphicon glyphicon-log-out">'
									. '</span> Выход',
								array(
									'class' => 'btn btn-primary',
									'type' => 'submit'
								)
							) ?>
						<?= CHtml::endForm() ?>
					</div>
				<?php } ?>
			</section>
		</nav>

		<section class = "container">
			<?= $content ?>

			<footer>
				<hr />
				<p>
					&copy; thewizardplusplus, <?= $copyright_years ?>
				<p>
			</footer>
		</section>

		<div class = "modal ajax-error-dialog">
			<div class = "modal-dialog">
				<div class = "modal-content">
					<div class = "modal-header">
						<button
							class = "close"
							type = "button"
							data-dismiss = "modal"
							aria-hidden = "true">
							&times;
						</button>
						<h4 class = "modal-title">
							<span class = "glyphicon glyphicon-remove-circle">
							</span>
							Ошибка!
						</h4>
					</div>

					<div class = "modal-body">
						<p>
							Во время AJAX-запроса произошла ошибка:
							<em class = "error-description"></em>.
						</p>
					</div>

					<div class = "modal-footer">
						<button
							class = "btn btn-default"
							type = "button"
							data-dismiss = "modal">
							OK
						</button>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
