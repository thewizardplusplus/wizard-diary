<?php
	/**
	 * @var StatsController $this
	 * @var array $data
	 * @var int $index
	 * @var CListView $widget
	 */
?>

<div class = "panel panel-success achievement-view">
	<h4>
		<time title = "<?= DateFormatter::formatDate($data['date']) ?>"><?=
			DateFormatter::formatMyDate($data['date'])
		?></time>:
		достижение &laquo;<?= $data['name'] ?>&raquo;
	</h4>
	<p>
		Выполнял пункт <strong>&laquo;<?= $data['point'] ?>&raquo;</strong>
		в течение <strong><?= $data['days'] ?></strong>.
	</p>
</div>
