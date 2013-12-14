<?php
	/* @var $this ParametersFormController */
	/* @var $model ParametersForm */
	/* @var $form CActiveForm */

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/datepicker.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/spinner.js'), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name . ' - Параметры';
?>

<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'parameters-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger'
	));
?>

<div class = "panel panel-default">
	<fieldset>
		<legend>Параметры:</legend>

		<?php echo $form->errorSummary($model, NULL, NULL, array('class' =>
			'alert alert-danger')); ?>

		<div class = "panel panel-default">
			<fieldset>
				<legend>Пароль:</legend>

				<div class = "form-group">
					<?php echo $form->labelEx($model, 'password'); ?>
					<?php
						echo $form->passwordField($model, 'password', array(
							'class' => 'form-control',
							'autocomplete' => 'off'
						));
					?>
					<?php echo $form->error($model, 'password'); ?>
				</div>

				<div class = "form-group">
					<?php echo $form->labelEx($model, 'password_copy'); ?>
					<?php
						echo $form->passwordField($model, 'password_copy',
							array(
								'class' => 'form-control',
								'autocomplete' => 'off'
							)
						); ?>
					<?php echo $form->error($model, 'password_copy'); ?>
				</div>
			</fieldset>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'start_date'); ?>
			<div class="input-group">
				<?php echo $form->numberField($model, 'start_date', array(
					'class' => 'form-control')); ?>
				<a class = "input-group-addon datapicker-show-button" href =
					"#"><span class = "glyphicon glyphicon-calendar"></span></a>
			</div>
			<?php echo $form->error($model, 'start_date'); ?>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'points_on_page'); ?>
			<?php echo $form->numberField($model, 'points_on_page', array(
					'class' => 'form-control',
					'min' => Parameters::MINIMUM_POINTS_ON_PAGE,
					'max' => Parameters::MAXIMUM_POINTS_ON_PAGE
				)); ?>
			<?php echo $form->error($model, 'points_on_page'); ?>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'versions_of_backups'); ?>
			<?php echo $form->numberField($model, 'versions_of_backups', array(
				'class' => 'form-control',
				'min' => Parameters::MINIMUM_VERSIONS_OF_BACKUPS,
				'max' => Parameters::MAXIMUM_VERSIONS_OF_BACKUPS
			)); ?>
			<?php echo $form->error($model, 'versions_of_backups'); ?>
		</div>

		<?php echo CHtml::submitButton('Сохранить', array('class' =>
			'btn btn-primary')); ?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>
