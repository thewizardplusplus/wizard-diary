<?php
	/**
	 * @var StatsController $this
	 * @var array $data
	 */

	Yii::app()->getClientScript()->registerPackage('jstree');

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var STATS_DATA = ' . json_encode($data) . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/stats_project_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика: список проектов';
?>

<header class = "page-header">
	<h4>Статистика: список проектов</h4>
</header>

<form class = "search-form" action = "#">
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
	<div class = "form-group">
		<div class = "input-group">
			<div class = "input-group-addon">
				<span class = "glyphicon glyphicon-copy"></span>
			</div>
			<input
				class = "form-control selected-points-text-view"
				placeholder = "Выбор..."
				readonly = "readonly" />
		</div>
	</div>
</form>

<div class = "stats-view project-list"></div>

<p class = "stats-view empty-label">
	Нет пунктов.
</p>
