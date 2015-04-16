<?php
	/**
	 * @var CController $this
	 */

	Yii::app()->getClientScript()->registerPackage('bootstrap');
	Yii::app()->getClientScript()->registerCssFile(
		CHtml::asset('styles/diary.css')
	);

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var CSRF_TOKEN = {'
			. '\'' . Yii::app()->request->csrfTokenName . '\':'
				. '\'' . Yii::app()->request->csrfToken . '\''
		. '};',
		CClientScript::POS_HEAD
	);
	if (!Yii::app()->user->isGuest) {
		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/ajax_error_dialog.js'),
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
										'label' => 'Ежедневно',
										'url' => array('dailyPoint/list')
									),
									array(
										'label' => 'Импорт',
										'url' => array('import/list')
									)
								),
								'htmlOptions' => array(
									'class' => 'nav navbar-nav'
								)
							)
						); ?>
						<ul class = "nav navbar-nav">
							<li class = "dropdown">
								<a
									href = "#"
									class = "dropdown-toggle"
									data-toggle = "dropdown">
									Статистика <span class = "caret"></span>
								</a>
								<?php $this->widget(
									'zii.widgets.CMenu',
									array(
										'items' => array(
											array(
												'label' => 'Ежедневные пункты',
												'url' => array(
													'stats/dailyPoints'
												)
											),
											array(
												'label' => 'Проекты',
												'url' => array('stats/projects')
											)
										),
										'htmlOptions' => array(
											'class' => 'dropdown-menu'
										)
									)
								); ?>
							</li>
						</ul>
						<ul class = "nav navbar-nav">
							<li class = "dropdown">
								<a
									href = "#"
									class = "dropdown-toggle"
									data-toggle = "dropdown">
									Прочее <span class = "caret"></span>
								</a>
								<?php $this->widget(
									'zii.widgets.CMenu',
									array(
										'items' => array(
											array(
												'label' => 'Бекапы',
												'url' => array('backup/list')
											),
											array(
												'label' => 'Лог доступа',
												'url' => array('access/list')
											),
											array(
												'label' => 'Параметры',
												'url' => array(
													'parameters/update'
												)
											)
										),
										'htmlOptions' => array(
											'class' => 'dropdown-menu'
										)
									)
								); ?>
							</li>
						</ul>
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

			<footer class = "small-text">
				<hr />
				<p>
					<?= Yii::app()->name ?>, <?= Constants::APP_VERSION ?><br />
					<span class = "unimportant-text">
						&copy; thewizardplusplus, <?= $copyright_years ?>
					</span>
				<p>
			</footer>
		</section>

		<?php if ($this->route == 'point/list') { ?>
			<nav class = "navbar navbar-default navbar-fixed-bottom age-points-button-container">
				<div class = "container">
					<button
						class = "btn btn-default navbar-btn navbar-right age-points-button"
						data-age-points-url = "<?= $this->createUrl('point/age') ?>">
						<img
							src = "<?= Yii::app()->request->baseUrl ?>/images/processing-icon.gif"
							alt = "..." />
						<span class = "glyphicon glyphicon-time"></span>
						<span>На день назад</span>
					</button>
				</div>
			</nav>
		<?php } ?>

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
