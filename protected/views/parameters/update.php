<?php
	/* @var $this ParametersFormController */
	/* @var $model ParametersForm */
	/* @var $form CActiveForm */
?>

<div class = "form">
	<?php
		$form = $this->beginWidget('CActiveForm', array(
			'id' => 'parameters-form',
			'enableAjaxValidation' => false,
			'enableClientValidation' => true
		));
	?>

	<fieldset>
		<legend>Параметры:</legend>

		<?php echo $form->errorSummary($model); ?>

		<fieldset>
			<legend>Пароль:</legend>

			<div class = "row">
				<?php echo $form->labelEx($model, 'password'); ?>
				<?php echo $form->textField($model, 'password'); ?>
				<?php echo $form->error($model, 'password'); ?>
			</div>

			<div class = "row">
				<?php echo $form->labelEx($model, 'password_copy'); ?>
				<?php echo $form->textField($model, 'password_copy'); ?>
				<?php echo $form->error($model, 'password_copy'); ?>
			</div>
		</fieldset>

		<div class="row">
			<?php echo $form->labelEx($model, 'points_on_page'); ?>
			<?php echo $form->numberField($model, 'points_on_page', array(
					'min' => Parameters::MINIMUM_POINTS_ON_PAGE,
					'max' => Parameters::MAXIMUM_POINTS_ON_PAGE
				)); ?>
			<?php echo $form->error($model, 'points_on_page'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Сохранить'); ?>
		</div>
	</fieldset>

	<?php $this->endWidget(); ?>
</div>

<?php
	echo CHtml::script(
		'jQuery("#ParametersForm_password").val("");' .
		'jQuery("#ParametersForm_password_copy").val("");'
	);
?>
