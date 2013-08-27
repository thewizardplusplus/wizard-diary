<?php
	/* @var $this PointController */
	/* @var $model Point */
	/* @var $form CActiveForm */
?>

<div class = "form">
	<?php
		$form = $this->beginWidget('CActiveForm', array(
			'id' => 'point-form',
			'enableAjaxValidation' => true,
		));
	?>
	<fieldset>
		<legend><?php echo ($model->isNewRecord ? 'Добавить пункт:' : 'Изменить'
			. ' пункт:'); ?></legend>
		<?php echo $form->errorSummary($model); ?>
		<div class = "row">
			<?php echo $form->labelEx($model, 'text'); ?>
			<?php echo $form->textArea($model, 'text', array('rows' => 6, 'cols'
				=> 50)); ?>
			<?php echo $form->error($model, 'text'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model, 'state'); ?>
			<?php echo $form->dropDownList($model, 'state', array('INITIAL' =>
				'Активный', 'SATISFIED' => 'Выполненный', 'NOT_SATISFIED' =>
				'Не выполненный', 'CANCELED' => 'Отменённый')); ?>
			<?php echo $form->error($model, 'state'); ?>
		</div>
		<div class = "row buttons">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' :
				'Сохранить'); ?>
		</div>
	</fieldset>
	<?php $this->endWidget(); ?>
</div>
