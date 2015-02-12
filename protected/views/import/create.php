<?php
	/* @var $this ImportController */
	/* @var $model Import */

	$this->pageTitle = Yii::app()->name . ' - Создать импорт';
?>

<header class = "page-header">
	<h4>Создать импорт</h4>
</header>

<?php $this->renderPartial('_form', array('model' => $model)); ?>
