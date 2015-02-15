<?php
	/* @var $this ImportController */
	/* @var $model Import */

	$this->pageTitle = Yii::app()->name . ' - Изменить импорт';
?>

<header class = "page-header">
	<h4>Изменить импорт</h4>
</header>

<?php $this->renderPartial('_form', array('model' => $model)); ?>
