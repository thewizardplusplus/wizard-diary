<?php
	/**
	 * @var SiteController $this
	 * @var AccessCodeForm $model
	 * @var string $access_code_container_class
	 * @var CActiveForm $form
	 */

	$this->pageTitle = Yii::app()->name . ' - Проверка кода доступа';
?>

<header class = "page-header">
	<h4>Проверка кода доступа</h4>
</header>

<p class = "alert alert-info">
	На твой телефон был выслан код доступа. Введи его в поле ниже.
</p>

<?php $form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'access-code-form',
		'focus' => array($model, 'access_code'),
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger'
	)
); ?>
	<?= $form->errorSummary(
			 $model,
			 NULL,
			 NULL,
			 array('class' => 'alert alert-danger')
	) ?>

	<div class = "form-group <?= $access_code_container_class ?>">
		<?= $form->labelEx(
				 $model,
				 'access_code',
				 array('class' => 'control-label')
		) ?>
		<?= $form->textField(
				 $model,
				 'access_code',
				 array('class' => 'form-control')
		) ?>
		<?= $form->error($model, 'access_code') ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-log-in"></span> Проверить',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
