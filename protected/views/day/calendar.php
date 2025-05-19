<?php
	/**
	 * @var DayController $this
	 * @var string $rest_days_prefix
	 * @var string $rest_days
	 * @var string $target_date
	 * @var string $target_my_date
	 */

	$this->pageTitle = Yii::app()->name . ' - Календарь';
?>

<p class = "unimportant-text italic-text without-bottom-margin">
	От текущей дюжины <?= $rest_days_prefix ?> <strong><?= $rest_days ?></strong>.
</p>
<p class = "unimportant-text italic-text">
	Текущая дюжина закончится
	<strong><time title = "<?= $target_date ?>"><?= $target_my_date ?></time></strong>.
</p>

<p>
	TODO
</p>
