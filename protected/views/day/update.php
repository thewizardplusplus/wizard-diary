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
		CHtml::asset('scripts/day_editor.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - ' . $my_date;
?>

<header class = "page-header clearfix">
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
	</h4>

	<p class = "unimportant-text italic-text">
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
