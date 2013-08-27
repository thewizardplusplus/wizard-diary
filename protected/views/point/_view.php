<?php
	/* @var $this PointController */
	/* @var $data Point */
?>

<div class = "view">
	<?php echo CHtml::encode($data->state); ?>: <?php echo CHtml::encode($data->
		date); ?> - <?php echo CHtml::encode($data->text); ?><br />
	<?php echo CHtml::link('Редактировать', array('update', 'id' => $data->
		id)); ?>
	<?php echo CHtml::ajaxLink('Удалить', $this->createUrl('point/delete',
		array('id' => $data->id, 'ajax' => 'delete')), array('type' => 'POST',
		'complete' => 'js:function() { $.fn.yiiListView.update(' .
		'"point_list"); }')); ?>
</div>
