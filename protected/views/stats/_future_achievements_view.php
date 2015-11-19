<?php
	/**
	 * @var StatsController $this
	 * @var array $data
	 * @var int $index
	 * @var CListView $widget
	 */
?>

<div class = "panel panel-info achievement-view">
	<h4>Достижение &laquo;<?= $data['name'] ?>&raquo;</h4>
	<p>
		Выполнение пункта <strong>&laquo;<?= $data['point'] ?>&raquo;</strong>
		в течение <strong><?= $data['days'] ?></strong>.
	</p>
</div>
