<?php
	/**
	 * @var SiteController $this
	 */

	$this->pageTitle = Yii::app()->name . ' - Сессии';
?>

<header class = "page-header">
	<h4>Сессии</h4>
</header>

<?= CHtml::beginForm($this->createUrl('site/logoutAll')) ?>
	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-trash"></span> Удалить все',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?= CHtml::endForm() ?>
