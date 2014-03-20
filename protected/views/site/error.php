<?php
	/**
	 * @var SiteController $this
	 * @var int $code the HTTP status code
	 * @var string $message the error message
	 */

	$this->pageTitle = Yii::app()->name . ' - Ошибка ' . $code;
?>

<div class = "alert alert-danger">
	<h4>Ошибка <?= $code ?></h4>
	<p><?= $message ?></p>
</div>
