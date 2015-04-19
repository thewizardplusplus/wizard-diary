<?php
	/**
	 * @var StatsController $this
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		'https://www.google.com/jsapi',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/stats_projects.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика: проекты';
?>

<header class = "page-header">
	<h4>Статистика: проекты</h4>
</header>

<div id = "project-dashboard-view">
	<div id = "project-filter-view"></div>
	<div id = "project-chart-view"></div>
</div>
