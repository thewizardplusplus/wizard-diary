<?php
	/**
	 * @var DayController $this
	 * @var CArrayDataProvider $data_provider
	 * @var string $my_date
	 * @var string $date
	 * @var string $raw_date
	 * @var array $stats
	 */

	Yii::app()->getClientScript()->registerPackage('ace');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_unit.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/close_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/day_editor.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - ' . $my_date;
?>

<header class = "page-header clearfix header-with-button">
	<button
		class = "btn btn-default pull-right close-button"
		title = "Закрыть"
		data-date = "<?= $date ?>"
		data-my-date = "<?= $my_date ?>"
		data-view-url = "<?=
			$this->createUrl('day/view', array('date' => $raw_date))
		?>">
		<span class = "glyphicon glyphicon-remove"></span>
	</button>
	<button
		class = "btn btn-primary pull-right save-day-button"
		title = "Сохранить"
		data-save-url = "<?=
			$this->createUrl('day/update', array('date' => $raw_date))
		?>">
		<img
			src = "<?=
				Yii::app()->request->baseUrl
			?>/images/processing-icon.gif"
			alt = "..." />
		<span class = "glyphicon glyphicon-floppy-disk"></span>
	</button>

	<h4 class = "clearfix day-editor-header">
		<time title = "<?= $date ?>"><?= $my_date ?></time>

		<span
			class = "label label-<?=
				$stats['completed']
					? 'success'
					: 'primary'
			?> day-completed-flag"
			title = "<?= $stats['completed'] ? 'Завершён' : 'Не завершён' ?>">
			<span
				class = "glyphicon glyphicon-<?=
					$stats['completed']
						? 'check'
						: 'unchecked'
				?>">
			</span>
		</span>

		<span
			class = "label label-success saved-flag"
			title = "Сохранено">
			<span class = "glyphicon glyphicon-floppy-saved"></span>
		</span>
	</h4>

	<p class = "pull-left unimportant-text italic-text number-of-points-view">
		<?= PointFormatter::formatNumberOfPoints($stats['projects']) ?>
	</p>
</header>

<?= CHtml::beginForm(
	$this->createUrl('day/update', array('date' => $raw_date)),
	'post',
	array('class' => 'day-form')
) ?>
	<div class = "form-group">
		<?= CHtml::label('Описание пунктов', 'points_description') ?>
		<div id = "day-editor"><?=
			CHtml::encode($points_description)
		?></div>
	</div>
<?= CHtml::endForm() ?>

<div class = "modal close-dialog">
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
					День <time class = "day-date"></time> не сохранён.
					Закрытие редактора может привести к потере последних
					изменений. Так что ты хочешь сделать?
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
