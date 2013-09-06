<?php
	/* @var $this SiteController */
	/* @var $error array */

	$this->pageTitle = Yii::app()->name . ' - Ошибка';
?>

<div class = "alert alert-danger">
	<h2>Ошибка <?php echo $code; ?></h2>
	<p><?php echo CHtml::encode($message); ?></p>
</div>
