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
		<canvas
			class = "media-object"
			width = "64"
			height = "64"
			data-jdenticon-hash = "<?= CHtml::encode($data['hash']) ?>">
		</canvas>
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

		<p class = "unimportant-text italic-text without-bottom-margin">
			Выполнялся уже <strong><?= $data['completed_days'] ?></strong>.
		</p>
		<p class = "unimportant-text italic-text without-bottom-margin">
			Осталось выполнять ещё <strong><?= $data['rest_days'] ?></strong>.
		</p>
		<p class = "unimportant-text italic-text">
			Будет получено
			<strong><time title = "<?= $data['date'] ?>"><?=
				$data['my_date']
			?></time></strong>.
		</p>
	</div>
</div>
