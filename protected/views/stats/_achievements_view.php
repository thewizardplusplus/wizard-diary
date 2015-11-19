<?php
	/**
	 * @var StatsController $this
	 * @var array $data
	 * @var int $index
	 * @var CListView $widget
	 */
?>

<div class = "panel panel-success media achievement-view">
	<div class = "media-left">
		<img
			class = "media-object"
			src = "https://placehold.it/64x64"
			alt = ""
			data-hash = "<?= CHtml::encode($data['hash']) ?>" />
	</div>
	<div class = "media-body">
		<h4 class = "media-heading">
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
</div>
