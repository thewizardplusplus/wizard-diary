<?php
	/**
	 * @var DayStateWidget $this
	 * @var string $date
	 * @var string $label_class
	 * @var string $label_title
	 * @var string $label_glyphicon
	 */
?>

<span
	class = "label <?= $label_class ?> day-completed-flag"
	title = "<?= $label_title ?>"
	data-stats-url = "<?= $this->controller->createUrl('day/stats', array(
		'date' => $date
	)) ?>">
	<span class = "glyphicon <?= $label_glyphicon ?>"></span>
</span>
