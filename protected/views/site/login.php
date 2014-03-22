<?php
	/**
	 * @var SiteController $this
	 * @var LoginForm $model
	 * @var string $password_container_class
	 * @var CActiveForm $form
	 */

	Yii::app()->getClientScript()->registerCssFile(
		CHtml::asset('jQueryFormStyler/jquery.formstyler.css')
	);
	Yii::app()->getClientScript()->registerCssFile(
		CHtml::asset('styles/custom_form_styler.css')
	);

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('jQueryFormStyler/jquery.formstyler.min.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/custom_captcha.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/custom_form_styler.js'),
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
			array('class' => 'form-control')
		) ?>
		<?= $form->error(
			$model,
			'password',
			array(
				'errorCssClass' => 'has-error',
				'successCssClass' => 'has-success'
			)
		) ?>
	</div>

	<div class = "form-group">
		<?= CHtml::activeLabelEx(
			$model,
			'verify_code',
			array('class' => 'control-label')
		) ?>
		<div class = "verify-code-container">
			<?php $this->widget(
				'CCaptcha',
				array(
					'imageOptions' => array(
						'id' => 'LoginForm_verify_code_image',
						'class' => 'img-thumbnail verify-code'
					),
					'showRefreshButton' => false,
					'clickableImage' => true
				)
			); ?>
			<button class = "btn btn-default" type = "button">
				<a href = "#" tabindex = "-1">
					<span class = "glyphicon glyphicon-refresh"></span>
				</a>
			</button>
		</div>
		<?= CHtml::activeTextField(
			$model,
			'verify_code',
			array('class' => 'form-control')
		) ?>
		<?= $form->error(
			$model,
			'verify_code',
			array(
				'errorCssClass' => 'has-error',
				'successCssClass' => 'has-success'
			)
		) ?>
	</div>

	<div class = "checkbox">
		<?= $form->checkBox($model, 'remember_me') ?>
		<?= $form->label($model, 'remember_me') ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-log-in"></span> Вход',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
