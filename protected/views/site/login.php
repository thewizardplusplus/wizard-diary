<?php
	/**
	 * @var SiteController $this
	 * @var LoginForm $model
	 * @var string $password_container_class
	 * @var string $verify_code_container_class
	 * @var CActiveForm $form
	 */

	require_once(__DIR__ . '/../../../recaptcha/recaptchalib.php');

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

	<div class = "form-group <?= $verify_code_container_class ?>">
		<?= CHtml::activeLabelEx(
			$model,
			'verify_code',
			array('class' => 'control-label')
		) ?>
		<div id = "recaptcha_widget" class = "panel panel-default">
			<div id = "recaptcha_image" class = "pull-left"></div>
			<a
				class = "btn btn-default pull-right recaptcha-refresh"
				href = "#"
				tabindex = "4">
				<span class = "glyphicon glyphicon-refresh"></span>
			</a>

			<input
				id = "recaptcha_response_field"
				class = "form-control"
				name = "recaptcha_response_field"
				tabindex = "2" />
			<?= $form->error($model, 'verify_code') ?>
		</div>
		<script
			src = "https://www.google.com/recaptcha/api/challenge?k=<?=
				Constants::RECAPTCHA_PUBLIC_KEY
			?>">
		</script>
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
