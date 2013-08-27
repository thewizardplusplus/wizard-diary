<?php
/* @var $this PointController */
/* @var $dataProvider CActiveDataProvider */

$this->pageTitle = Yii::app()->name;

$this->widget('ext.groupgridview.GroupGridView', array(
	'dataProvider' => $dataProvider,
	'extraRowColumns' => array('date'),
	'extraRowPos' => 'above',
	'columns' => array(
		array('class' => 'PointColumn', 'cssClassExpression' => 'strtolower(' .
			'$data->state)'),
		array('class' => 'CButtonColumn', 'buttons' => array('view' => array(
			'visible' => 'FALSE'),'update' => array('label' => 'Изменить'),
			'delete' => array('label' => 'Удалить')), 'deleteConfirmation' =>
			'Вы уверены, что хотите удалить данный пункт?')
	),
	'hideHeader' => TRUE
));
