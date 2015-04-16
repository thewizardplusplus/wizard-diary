<?php
	/**
	 * @var StatsController $this
	 */

	Yii::app()->getClientScript()->registerPackage('chart.js');
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/stats.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика - Проекты';
?>

<header class = "page-header">
	<h4>Статистика &mdash; Проекты</h4>
</header>

<canvas class = "stats-view projects"></canvas>
