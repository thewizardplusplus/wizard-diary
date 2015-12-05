<?php
	/**
	 * @var DailyPointController $this
	 * @var string $points_description
	 */

	Yii::app()->getClientScript()->registerPackage('ace');
	Yii::app()->getClientScript()->registerPackage('ace-language-tools');
	Yii::app()->getClientScript()->registerPackage('mobile-detect');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/daily_point_close_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/daily_point_editor.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Ежедневно';
?>

<header class = "page-header clearfix header-with-button">
	<button
		class = "btn btn-default pull-right close-button"
		title = "Закрыть"
		data-view-url = "<?= $this->createUrl('dailyPoint/list') ?>">
		<span class = "glyphicon glyphicon-remove"></span>
	</button>
	<button
		class = "btn btn-primary pull-right save-day-button"
		title = "Сохранить"
		data-save-url = "<?= $this->createUrl('dailyPoint/update') ?>">
		<img
			src = "<?=
				Yii::app()->request->baseUrl
			?>/images/processing-icon.gif"
			alt = "..." />
		<span class = "glyphicon glyphicon-floppy-disk"></span>
	</button>

	<h4>
		Ежедневно

		<span
			class = "label label-success saved-flag"
			title = "Сохранено">
			<span class = "glyphicon glyphicon-floppy-saved"></span>
		</span>
	</h4>
</header>

<?= CHtml::beginForm(
	$this->createUrl('dailyPoint/update'),
	'post',
	array('class' => 'daily-point-editor-form')
) ?>
	<div class = "form-group">
		<?= CHtml::label('Описание пунктов', 'points_description') ?>

		<div>
			<ul class = "nav nav-tabs">
				<li class = "active">
					<a href = "#default" data-toggle = "tab">По умолчанию</a>
				</li>
				<li>
					<a href = "#mobile" data-toggle = "tab">Мобильный</a>
				</li>
			</ul>

			<div class = "tab-content">
				<div id = "default" class = "tab-pane active">
					<div id = "daily-point-editor"><?=
						CHtml::encode($points_description)
					?></div>
				</div>
				<div id = "mobile" class = "tab-pane">
					<?= CHtml::textArea(
						'daily-point-mobile-editor',
						$points_description,
						array('class' => 'form-control')
					) ?>
				</div>
			</div>
		</div>
	</div>
<?= CHtml::endForm() ?>

<div class = "modal daily-point-close-dialog">
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
					<span class = "glyphicon glyphicon-warning-sign"></span>
					Внимание!
				</h4>
			</div>

			<div class = "modal-body">
				<p>
					Ежедневные пункты не сохранены. Закрытие редактора может
					привести к потере последних изменений. Так что ты хочешь
					сделать?
				</p>
			</div>

			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary save-button">
					<span class = "glyphicon glyphicon-floppy-disk"></span>
					Сохранить и закрыть
				</button>
				<button type = "button" class = "btn btn-danger close-button">
					<span class = "glyphicon glyphicon-remove"></span>
					Закрыть
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
