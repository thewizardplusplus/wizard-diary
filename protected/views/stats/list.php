<?php
/**
 * @var StatsController $this
 */

Yii::app()->getClientScript()->registerPackage('jquery.ui');

$this->pageTitle = Yii::app()->name . ' - Статистика';

$this->widget(
	'zii.widgets.jui.CJuiTabs',
	array(
		'tabs' => array(
			'Ежедневные пункты' => array(
				'ajax' => $this->createAbsoluteUrl('stats/dailyPoints')
			),
			'Проекты' => array(
				'ajax' => $this->createAbsoluteUrl('stats/projects')
			)
		)
	)
);
