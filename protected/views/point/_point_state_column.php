<?php
	/**
	 * @var PointStateColumn $this
	 * @var string $state_class
	 * @var string $state_icon_name
	 * @var int $point_id
	 * @var string $update_url
	 */
?>

<div class = "dropdown">
	<a class = "state-<?= $state_class ?>" href = "#" data-toggle = "dropdown">
		<span class = "glyphicon glyphicon-<?= $state_icon_name ?>"></span>
	</a>

	<ul
		class = "dropdown-menu"
		data-update-url = "<?= $update_url ?>"
		aria-labelledby = "point<?= $point_id ?>-state-label">
		<li>
			<a
				class = "state-initial"
				href = "#"
				tabindex = "-1"
				data-state = "INITIAL">
				<span class = "glyphicon glyphicon-exclamation-sign"></span>
				Активный
			</a>
		</li>
		<li>
			<a
				class = "state-satisfied"
				href = "#"
				tabindex = "-1"
				data-state = "SATISFIED">
				<span class = "glyphicon glyphicon-ok-sign"></span>
				Выполнен
			</a>
		</li>
		<li>
			<a
				class = "state-not-satisfied"
				href = "#"
				tabindex = "-1"
				data-state = "NOT_SATISFIED">
				<span class = "glyphicon glyphicon-remove-sign"></span>
				Не выполнен
			</a>
		</li>
		<li>
			<a
				class = "state-canceled"
				href = "#"
				tabindex = "-1"
				data-state = "CANCELED">
				<span class = "glyphicon glyphicon-minus-sign"></span>
				Отменён
			</a>
		</li>
	</ul>
</div>
