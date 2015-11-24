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
		CHtml::asset('scripts/stats_points.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика: пункты';
?>

<header class = "page-header">
	<h4>Статистика: пункты</h4>
</header>

<p class = "mean-view">
	Среднее количество:
	<strong><?= number_format($mean, 2, '.', '') ?> пункта</strong>.
</p>

<div class = "stats-view points"></div>

<p class = "stats-view empty-label">
	Нет пунктов.
</p>
