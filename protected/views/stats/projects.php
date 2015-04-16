<?php
	/**
	 * @var StatsController $this
	 */

	Yii::app()->getClientScript()->registerPackage('chart.js');
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/stats_projects.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика: проекты';
?>

<header class = "page-header">
	<h4>Статистика: проекты</h4>
</header>

<canvas class = "stats-view projects"></canvas>
