<?php
	/**
	 * @var PointController $this
	 */

	Yii::app()->getClientScript()->registerPackage('jquery.ui');
	Yii::app()->getClientScript()->registerPackage(
		'awesome-bootstrap-checkbox'
	);
	Yii::app()->getClientScript()->registerPackage('jstree');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_unit.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/delete_points_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/delete_points.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Удаление пунктов';
?>

<header class = "page-header">
	<h4>Удаление пунктов</h4>
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
			<div class = "input-group-addon search-input-cleaning-button">
				<span class = "glyphicon glyphicon-remove"></span>
			</div>
		</div>
	</div>

	<div class = "checkbox checkbox-primary">
		<?= CHtml::checkBox(
			'search_from_beginning',
			true,
			array('class' => 'search-from-beginning')
		) ?>
		<?= CHtml::label('Поиск с начала', 'search_from_beginning') ?>
	</div>
</form>

<div class = "clearfix points-found-controls-view">
	<?= CHtml::beginForm(
		$this->createUrl('point/deleteBatch'),
		'post',
		array('class' => 'form-inline pull-right delete-points-form')
	) ?>
		<?= CHtml::htmlButton(
			'<span class = "glyphicon glyphicon-trash"></span> '
				. 'Удалить найденные пункты',
			array(
				'class' => 'btn btn-danger delete-button',
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

<div class = "modal delete-points-dialog" tabindex = "-1">
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
					Ты точно хочешь удалить найденные пункты?
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
