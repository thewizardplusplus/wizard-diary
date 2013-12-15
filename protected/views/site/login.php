<?php
	/* @var $this SiteController */
	/* @var $model LoginForm */
	/* @var $form CActiveForm  */

	Yii::app()->getClientScript()->registerCssFile(CHtml::asset(
		'jQueryFormStyler/jquery.formstyler.css'));
	Yii::app()->getClientScript()->registerCssFile(CHtml::asset(
		'styles/styler.css'));

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'jQueryFormStyler/jquery.formstyler.min.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/styler.js'), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name;
?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'login-form',
	'enableAjaxValidation' => true,
	'enableClientValidation' => true,
	'errorMessageCssClass' => 'alert alert-danger'
)); ?>
	<fieldset>
		<legend>Вход:</legend>

		<?php echo $form->errorSummary($model, NULL, NULL, array('class' =>
			'alert alert-danger')); ?>

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
<?php $this->endWidget(); ?>
