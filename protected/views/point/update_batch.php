<?php
	/**
	 * @var PointController $this
	 */

	Yii::app()->getClientScript()->registerPackage('jstree');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_unit.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/update_points_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/update_points.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Изменение пунктов';
?>

<header class = "page-header">
	<h4>Изменение пунктов</h4>
</header>

<form
	class = "search-points-form"
	action = "#"
	data-find-url = "<?= $this->createUrl('point/find') ?>">
	<div class = "form-group">
		<div class = "input-group">
			<div class = "input-group-addon">
				<span class = "glyphicon glyphicon-search"></span>
			</div>
			<input
				class = "form-control search-input"
				placeholder = "Поиск..." />
			<div class = "input-group-addon clean-button">
				<span class = "glyphicon glyphicon-remove"></span>
			</div>
		</div>
	</div>
</form>

<div class = "clearfix points-found-controls-view">
	<?= CHtml::beginForm(
		$this->createUrl('point/updateBatch'),
		'post',
		array('class' => 'form-inline pull-right update-points-form')
	) ?>
		<?= CHtml::htmlButton(
			'<span class = "glyphicon glyphicon-pencil"></span> '
				. 'Изменить найденные пункты',
			array(
				'class' => 'btn btn-primary update-button',
				'type' => 'submit'
			)
		) ?>
	<?= CHtml::endForm() ?>

	<p class = "pull-left">
		Найден<span class="quantity-label-plural-form">о</span>
		<strong><span class="points-quantity-view"></span></strong>
		в <strong><span class="days-quantity-view"></span></strong>.
	</p>
</div>

<p class = "points-found-empty-view">
	Пункты не найдены.
</p>

<div class = "points-found-view"></div>

<div class = "modal update-points-dialog" tabindex = "-1">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button
					class = "close"
					type = "button"
					data-dismiss = "modal"
					aria-hidden = "true">
					&times;
				</button>
				<h4 class = "modal-title">
					<span class = "glyphicon glyphicon-warning-sign"></span>
					Внимание!
				</h4>
			</div>

			<div class = "modal-body">
				<p>
					Ты точно хочешь изменить найденные пункты?
				</p>
			</div>

			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary ok-button">
					OK
				</button>
				<button
					class = "btn btn-default"
					type = "button"
					data-dismiss = "modal">
					Отмена
				</button>
			</div>
		</div>
	</div>
</div>
