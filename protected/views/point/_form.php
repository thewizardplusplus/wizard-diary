<?php
	/* @var $this PointController */
	/* @var $model Point */
	/* @var $form CActiveForm */
?>

<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'point-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger'
	));
?>

<div class = "panel panel-default">
	<fieldset>
		<legend>
			<?php echo ($model->isNewRecord ? 'Добавить пункт:' : 'Изменить'
				. ' пункт:'); ?>
		</legend>

		<?php echo $form->errorSummary($model); ?>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'text'); ?>
			<?php echo $form->textField($model, 'text', array('class' =>
				'form-control')); ?>
			<?php echo $form->error($model, 'text'); ?>
		</div>

		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' :
			'Сохранить', array('class' => 'btn btn-primary')); ?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>
