<?php
	/**
	 * @var DayController $this
	 * @var CArrayDataProvider $data_provider
	 * @var string $my_date
	 * @var string $date
	 * @var string $raw_date
	 * @var array $stats
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		Yii::app()->request->baseUrl . '/scripts/ace/ace.js',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_unit.js'),
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

	<h4>
		<time title = "<?= $date ?>"><?= $my_date ?></time>

		<span
			class = "label label-<?=
				$stats['completed']
					? 'success'
					: 'primary'
			?> day-completed-flag">
			<?=
				$stats['completed']
					? 'Завершён'
					: 'Не завершён'
			?>
		</span>

		<span
			class = "label label-success saved-flag"
			title = "Сохранено">
			<span class = "glyphicon glyphicon-floppy-saved"></span>
		</span>
	</h4>

	<p class = "unimportant-text italic-text number-of-points-view">
		<?= PointFormatter::formatNumberOfPoints($stats['projects']) ?>
	</p>
</header>

<?= CHtml::beginForm(
	$this->createUrl('day/update', array('date' => $raw_date))
) ?>
	<div class = "form-group">
		<?= CHtml::label('Описание пунктов', 'points_description') ?>
		<div id = "day-editor"><?=
			CHtml::encode($points_description)
		?></div>
	</div>
<?= CHtml::endForm() ?>
