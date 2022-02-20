<?php
	/**
	 * @var SiteController $this
	 * @var LoginForm $model
	 * @var string $password_container_class
	 * @var string $verify_code_container_class
	 * @var CActiveForm $form
	 */

	Yii::app()->getClientScript()->registerPackage(
		'awesome-bootstrap-checkbox'
	);

	Yii::app()->getClientScript()->registerScriptFile(
		'https://www.google.com/recaptcha/api.js'
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/custom_recaptcha.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Вход';
?>

<header class = "page-header">
	<h4>Вход</h4>
</header>

<?php $form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'login-form',
		'focus' => array($model, 'password'),
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

	<div class = "form-group <?= $password_container_class ?>">
		<?= $form->labelEx(
			$model,
			'password',
			array('class' => 'control-label')
		) ?>
		<?= $form->passwordField(
			$model,
			'password',
			array('class' => 'form-control', 'tabindex' => 1)
		) ?>
		<?= $form->error($model, 'password') ?>
	</div>

	<div class="form-group <?= $verify_code_container_class ?>">
		<?= $form->labelEx(
			$model,
			'verify_code',
			array('class' => 'control-label')
		) ?>
		<div
			class="g-recaptcha"
			data-sitekey="<?= Constants::RECAPTCHA_PUBLIC_KEY ?>"
			data-callback="process_recaptcha_response"></div>
		<?= $form->hiddenField($model, 'verify_code') ?>
		<?= $form->error($model, 'verify_code') ?>
	</div>

	<div class = "checkbox checkbox-primary">
		<?= $form->checkBox($model, 'need_remember') ?>
		<?= $form->labelEx($model, 'need_remember') ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-log-in"></span> Вход',
		array(
			'class' => 'btn btn-primary login-button',
			'type' => 'submit',
			'tabindex' => 3
		)
	) ?>
<?php $this->endWidget(); ?>
