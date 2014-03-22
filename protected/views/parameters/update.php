<?php
	/**
	 * @var ParametersController $this
	 * @var ParametersForm $model
	 * @var string $password_container_class
	 * @var string $password_copy_container_class
	 * @var CActiveForm $form
	 */

	$this->pageTitle = Yii::app()->name . ' - Параметры';
?>

<header class = "page-header visible-xs">
	<h4>Параметры</h4>
</header>

<?php $form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'parameters-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger',
		'clientOptions' => array(
			'errorCssClass' => 'has-error',
			'successCssClass' => 'has-success'
		)
	)
); ?>
	<?= $form->errorSummary(
		$model,
		NULL,
		NULL,
		array('class' => 'alert alert-danger')
	) ?>

	<div class = "form-group<?= $password_container_class ?>">
		<?= $form->labelEx(
			$model,
			'password',
			array('class' => 'control-label')
		) ?>
		<?= $form->passwordField(
			$model,
			'password',
			array(
				'class' => 'form-control',
				'autocomplete' => 'off'
			)
		) ?>
		<?= $form->error($model, 'password') ?>
	</div>

	<div class = "form-group<?= $password_copy_container_class ?>">
		<?= $form->labelEx(
			$model,
			'password_copy',
			array('class' => 'control-label')
		) ?>
		<?= $form->passwordField(
			$model,
			'password_copy',
			array(
				'class' => 'form-control',
				'autocomplete' => 'off'
			)
		) ?>
		<?= $form->error($model, 'password_copy') ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-floppy-disk"></span> Сохранить',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
