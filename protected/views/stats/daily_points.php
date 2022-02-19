<?php
	/**
	 * @var StatsController $this
	 * @var array $data
	 * @var float $mean
	 */

	Yii::app()->getClientScript()->registerPackage('moment');

	Yii::app()->getClientScript()->registerCssFile(
		CHtml::asset('chap-links-library/graph/graph.css')
	);

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var STATS_DATA = ' . json_encode($data) . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		'https://www.google.com/jsapi',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('chap-links-library/graph/graph.min.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_unit.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/chap_links_custom_labels.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/stats_daily_points.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика: ежедневные пункты';
?>

<header class = "page-header">
	<h4>Статистика: ежедневные пункты</h4>
</header>

<p class = "mean-view">
	Среднее выполнение:
	<strong><?= number_format($mean, 2, '.', '') ?>%</strong>.
</p>

<div class = "stats-view daily-points"></div>

<p class = "stats-view empty-label">
	Нет ежедневных пунктов.
</p>
