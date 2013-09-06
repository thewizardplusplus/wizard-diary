<?php

/* @var $this PointController */
/* @var $model Point */
/* @var $dataProvider CActiveDataProvider */

$this->pageTitle = Yii::app()->name;

$this->renderPartial('_form', array('model' => $model));

$this->widget('ext.groupgridview.GroupGridView', array(
	'id' => 'point_list',
	'dataProvider' => $dataProvider,
	'extraRowColumns' => array('date'),
	'extraRowExpression' => '"<span style = \"font-weight: bold; ' .
		'text-decoration: underline;\">" . implode(".", array_reverse(' .
		'explode("-", $data->date))) . ":</span>"',
	'extraRowPos' => 'above',
	'columns' => array(
		array(
			'class' => 'PointStateColumn',
			'htmlOptions' => array('style' => 'width: 125px; text-align: ' .
				'center;')
		),
		array(
			'type' => 'html',
			'value' => '"<span class = \"state-" . strtolower(str_replace("_", '
				. '"-", $data->state)) . "\">" . $data->text . "</span>"'
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
					'deleteConfirmation' => 'Вы уверены, что хотите удалить ' .
					'данный пункт?'
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
