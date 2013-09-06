<?php
	/* @var $this SiteController */
	/* @var $model LoginForm */
	/* @var $form CActiveForm  */

	$this->pageTitle = Yii::app()->name;
?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'login-form',
	'enableAjaxValidation' => true,
	'enableClientValidation' => true,
	'errorMessageCssClass' => 'alert alert-danger'
)); ?>

<div class = "panel panel-default">
	<fieldset>
		<legend>Вход:</legend>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'password'); ?>
			<?php echo $form->passwordField($model, 'password', array('class' =>
				'form-control')); ?>
			<?php echo $form->error($model, 'password'); ?>
		</div>

		<div class = "checkbox">
			<?php echo $form->checkBox($model,'remember_me'); ?>
			<?php echo $form->label($model,'remember_me'); ?>
			<?php echo $form->error($model,'remember_me'); ?>
		</div>

		<?php echo CHtml::submitButton('Вход', array('class' =>
			'btn btn-primary')); ?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>

<?php
	echo CHtml::script(
		'jQuery("#LoginForm_remember_me").styler();' .
		'jQuery(".jq-checkbox div").addClass("glyphicon glyphicon-ok");'
	);
?>
