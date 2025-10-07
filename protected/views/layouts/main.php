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
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var RAW_CSRF_TOKEN = \'' . Yii::app()->request->csrfToken . '\';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var SAVE_BACKUPS_TO_DROPBOX = '
			. (Parameters::getModel()->save_backups_to_dropbox ? 'true' : 'false')
			. ';',
		CClientScript::POS_HEAD
	);
	if (!Yii::app()->user->isGuest) {
		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/ajax_error_dialog.js'),
			CClientScript::POS_HEAD
		);

		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/backup_dialog.js'),
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
						<ul class = "nav navbar-nav">
							<li class = "dropdown">
								<a
									href = "#"
									class = "dropdown-toggle"
									data-toggle = "dropdown">
									Пункты <span class = "caret"></span>
								</a>
								<?php $this->widget(
									'zii.widgets.CMenu',
									array(
										'items' => array(
											array(
												'label' => 'Ежедневные',
												'url' => array(
													'dailyPoint/list'
												)
											),
											array(
												'label' => '',
												'itemOptions' => array(
													'class' => 'divider'
												)
											),
											array(
												'label' => 'Импорт',
												'url' => array('day/import')
											),
											array(
												'label' => 'Изменение',
												'url' => array('point/updateBatch')
											),
											array(
												'label' => 'Удаление',
												'url' => array('point/deleteBatch')
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
												'label' => 'Пункты',
												'url' => array('stats/points')
											),
											array(
												'label' => '',
												'itemOptions' => array(
													'class' => 'divider'
												)
											),
											array(
												'label' => 'Прогресс проектов (краткий)',
												'url' => array(
													'stats/projects',
													'tasks_required' => false
												)
											),
											array(
												'label' => 'Прогресс проектов (полный)',
												'url' => array(
													'stats/projects',
													'tasks_required' => true
												)
											),
											array(
												'label' => '',
												'itemOptions' => array(
													'class' => 'divider'
												)
											),
											array(
												'label' =>
													'Список ежедневных пунктов',
												'url' => array(
													'stats/dailyPointList'
												)
											),
											array(
												'label' => 'Список проектов',
												'url' => array(
													'stats/projectList'
												)
											),
											array(
												'label' => 'Список действий',
												'url' => array(
													'stats/projectActions'
												)
											),
											array(
												'label' => '',
												'itemOptions' => array(
													'class' => 'divider'
												)
											),
											array(
												'label' => 'Достижения',
												'url' => array(
													'stats/achievements'
												)
											),
											array(
												'label' => 'Будущие достижения',
												'url' => array(
													'stats/futureAchievements'
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
												'label' => '',
												'itemOptions' => array(
													'class' => 'divider'
												)
											),
											array(
												'label' => 'Ошибки',
												'url' => array('mistake/list')
											),
											array(
												'label' => 'Словарь',
												'url' => array('spelling/list')
											),
											array(
												'label' => '',
												'itemOptions' => array(
													'class' => 'divider'
												)
											),
											array(
												'label' => 'Лог доступа',
												'url' => array('access/list')
											),
											array(
												'label' => 'Белый список',
												'url' => array(
													'access/whitelist'
												)
											),
											array(
												'label' => '',
												'itemOptions' => array(
													'class' => 'divider'
												)
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
						<button
							class =
								"btn btn-primary navbar-btn create-backup-button"
							data-create-backup-url =
								"<?= $this->createUrl('backup/create') ?>"
							data-save-backup-url =
								"<?= $this->createUrl('backup/save') ?>"
							data-dropbox-app-key =
								"<?= Constants::DROPBOX_APP_KEY ?>"
							data-dropbox-redirect-url =
								"<?= Constants::DROPBOX_REDIRECT_URL ?>">
							<img
								src = "<?=
									Yii::app()->request->baseUrl
								?>/images/processing-icon.gif"
								alt = "..." />
							<span class = "glyphicon glyphicon-compressed">
							</span>
							<span>Бекап</span>
						</button>
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

			<footer class = "clearfix small-text">
				<hr />
				<div class = "pull-right">
					<p
						class = "unimportant-text italic-text without-bottom-margin">
						Страница отрендерена за <strong><?=
							round(Yii::getLogger()->executionTime, 2)
						?> с</strong>.
					</p>
					<p
						class = "unimportant-text italic-text without-bottom-margin">
						Использовано <strong><?=
							round(
								Yii::getLogger()->memoryUsage
									/ (1024.0 * 1024.0),
								2
							)
						?> МиБ</strong> ОЗУ.
					</p>
					<p class = "unimportant-text italic-text">
						Совершено <strong><?=
							RequestFormatter::formatRequests(
								Yii::app()->db->getStats()[0]
							)
						?></strong> к БД.
					</p>
				</div>

				<p class = "without-bottom-margin">
					<?= Yii::app()->name ?>, <?= Constants::APP_VERSION ?>
				</p>
				<p class = "unimportant-text">
					Copyright &copy; <?= $copyright_years ?> thewizardplusplus
				</p>
			</footer>
		</section>

		<div class = "modal ajax-error-dialog" tabindex = "-1">
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

		<div class = "modal backup-dialog" tabindex = "-1">
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
							<span class = "glyphicon glyphicon-warning-sign">
							</span>

							Внимание!
						</h4>
					</div>

					<div class = "modal-body">
						<p>
							Ты точно хочешь создать бекап?
						</p>
					</div>

					<div class = "modal-footer">
						<button
							type = "button"
							class = "btn btn-primary ok-button">
							OK
						</button>
						<button
							class = "btn btn-default"
							type = "button"
							data-dismiss = "modal">
							Отмена
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class = "modal finishing-dialog" tabindex = "-1">
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
							<span class = "glyphicon glyphicon-warning-sign">
							</span>

							Внимание!
						</h4>
					</div>

					<div class = "modal-body">
						<p>
							Ты точно хочешь завершить день?
						</p>
					</div>

					<div class = "modal-footer">
						<button
							type = "button"
							class = "btn btn-primary ok-button">
							OK
						</button>
						<button
							class = "btn btn-default"
							type = "button"
							data-dismiss = "modal">
							Отмена
						</button>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
