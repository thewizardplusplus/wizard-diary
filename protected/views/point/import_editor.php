<?php
	/**
	 * @var PointController $this
	 */

	$this->pageTitle = Yii::app()->name . ' - Импорт пунктов';
?>

<header class = "page-header visible-xs">
	<h4>Импорт пунктов</h4>
</header>

<?= CHtml::beginForm() ?>
	<div class = "form-group">
		<?= CHtml::label(
			'Описание пунктов',
			'points-description',
			array('class' => 'control-label')
		) ?>
		<?= CHtml::textArea(
			'points-description',
			'',
			array('class' => 'form-control', 'rows' => '12')
		) ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-upload"></span> Импорт',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?= CHtml::endForm() ?>
