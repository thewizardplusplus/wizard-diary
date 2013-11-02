<?php
	/* @var $this PointController */
	/* @var $model Point */
	/* @var $form CActiveForm */

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/specialCaseOfAdding.js'), CClientScript::POS_HEAD);
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

		<?php echo $form->errorSummary($model, NULL, NULL, array('class' =>
			'alert alert-danger')); ?>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'text'); ?>
			<?php echo $form->textField($model, 'text', array('class' =>
				'form-control')); ?>
			<?php echo $form->error($model, 'text'); ?>
		</div>

		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' :
			'Сохранить', array('class' => 'btn btn-primary')); ?>
		<?php
			if ($model->isNewRecord) {
				echo Chtml::hiddenField('Point[state]', 'INITIAL');
				echo CHtml::button('Добавить как выполненный', array(
					'class' => 'special-case btn btn-default'));
			}
		?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>
