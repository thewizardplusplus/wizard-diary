<?php
/* @var $this PointController */
/* @var $dataProvider CActiveDataProvider */

$this->pageTitle = Yii::app()->name;

$this->renderPartial('_form', array('model' => $model));

$this->widget('ext.groupgridview.GroupGridView', array(
	'dataProvider' => $dataProvider,
	'extraRowColumns' => array('date'),
	'extraRowExpression' => '"<span style = \"font-weight: bold; ' .
		'text-decoration: underline;\">" . implode(".", array_reverse(' .
		'explode("-", $data->date))) . ":</span>"',
	'extraRowPos' => 'above',
	'columns' => array(
		array(
			'class' => 'PointColumn',
			'cssClassExpression' => 'strtolower($data->state)'
		),
		array(
			'class' => 'CButtonColumn',
			'buttons' => array(
				'view' => array('visible' => 'FALSE'),
				'update' => array(
					'label' => 'Изменить',
					'visible' => '!empty($data->text)'
				),
				'delete' => array('label' => 'Удалить')),
				'deleteConfirmation' => 'Вы уверены, что хотите удалить данный '
					. 'пункт?'
		)
	),
	'hideHeader' => TRUE,
	'pager' => array(
		'firstPageLabel' => '&lt;&lt;',
		'prevPageLabel' => '&lt;',
		'nextPageLabel' => '&gt;',
		'lastPageLabel' => '&gt;&gt;',
	)
));
