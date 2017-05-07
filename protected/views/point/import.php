<?php
	/**
	 * @var PointController $this
	 */

	$this->pageTitle = Yii::app()->name . ' - Импорт пунктов';
?>

<header class = "page-header clearfix header-with-button">
	<?= CHtml::beginForm(
		$this->createUrl('point/import'),
		'post',
		array('class' => 'form-inline pull-right import-points-form')
	) ?>
		<?= CHtml::htmlButton(
			'<span class = "glyphicon glyphicon-import"></span> Импортировать',
			array(
				'class' => 'btn btn-primary import-button',
				'type' => 'submit',
				'disabled' => 'disabled',
			)
		) ?>
	<?= CHtml::endForm() ?>

	<h4>Импорт пунктов</h4>
</header>
