<?php
	/**
	 * @var SiteController $this
	 * @var int $code the HTTP status code
	 * @var string $message the error message
	 */

	$this->pageTitle = Yii::app()->name . ' - Ошибка ' . CHtml::encode($code);
?>

<div class = "alert alert-danger">
	<h4>Ошибка <?= CHtml::encode($code) ?></h4>
	<p><?= CHtml::encode($message) ?></p>
</div>
