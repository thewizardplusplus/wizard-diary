<?php
	/**
	 * @var DayController $this
	 */

	Yii::app()->getClientScript()->registerPackage('ace');
	Yii::app()->getClientScript()->registerPackage('mobile-detect');

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/import_editor.js'
	), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name . ' - Импорт пунктов';
?>

<header class="page-header clearfix header-with-button">
	<button
		class="btn btn-primary pull-right import-button"
		title="Импортировать">
		<span class="glyphicon glyphicon-import"></span> Импортировать
	</button>

	<h4>Импорт пунктов</h4>
</header>

<?= CHtml::beginForm($this->createUrl('day/import'), 'post', array(
	'id' => 'import-form',
)) ?>
	<div class="form-group">
		<label>Описание пунктов</label>

		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#default" data-toggle="tab">По умолчанию</a>
			</li>
			<li>
				<a href="#mobile" data-toggle="tab">Мобильный</a>
			</li>
		</ul>

		<div class="tab-content">
			<div id="default" class="tab-pane active">
				<div id="import-editor"></div>
			</div>
			<div id="mobile" class="tab-pane">
				<textarea
					id="import-mobile-editor"
					class="form-control"></textarea>
			</div>
		</div>
	</div>

	<input id="points-description" name="points-description" type="hidden" />
<?= CHtml::endForm() ?>
