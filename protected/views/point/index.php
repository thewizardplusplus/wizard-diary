<?php
/* @var $this PointController */
/* @var $dataProvider CActiveDataProvider */

$this->pageTitle = Yii::app()->name;

$this->widget('zii.widgets.CListView', array(
	'id' => 'point_list',
	'dataProvider' => $dataProvider,
	'itemView' => '_view',
));
