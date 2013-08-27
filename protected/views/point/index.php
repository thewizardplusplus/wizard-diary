<?php
/* @var $this PointController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points',
);

$this->menu=array(
	array('label'=>'Create Point', 'url'=>array('create')),
	array('label'=>'Manage Point', 'url'=>array('admin')),
);
?>

<h1>Points</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
