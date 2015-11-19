<?php
	/**
	 * @var StatsController $this
	 * @var array $data
	 * @var int $index
	 * @var CListView $widget
	 */
?>

<div class = "panel panel-info media achievement-view">
	<div class = "media-left">
		<img
			class = "media-object"
			src = "https://placehold.it/64x64"
			alt = ""
			data-hash = "<?= CHtml::encode($data['hash']) ?>" />
	</div>
	<div class = "media-body">
		<h4 class = "media-heading">
			Достижение &laquo;<?= $data['name'] ?>&raquo;
		</h4>
		<p>
			Выполнение пункта <strong>&laquo;<?=
				$data['point']
			?>&raquo;</strong>
			в течение <strong><?= $data['days'] ?></strong>.
		</p>
	</div>
</div>
