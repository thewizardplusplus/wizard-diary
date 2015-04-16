<?php
	/**
	 * @var StatsController $this
	 * @var array $data
	 */

	Yii::app()->getClientScript()->registerPackage('chart.js');
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var STATS_DATA = ' . json_encode($data) . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/stats_daily_points.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика - Ежедневные пункты';
?>

<header class = "page-header">
	<h4>Статистика &mdash; Ежедневные пункты</h4>
</header>

<canvas class = "stats-view daily-points"></canvas>
