<?php
	/**
	 * @var SiteController $this
	 * @var AccessCodeForm $model
	 * @var int $access_code_lifetime
	 * @var string $access_code_container_class
	 * @var CActiveForm $form
	 */

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var ACCESS_CODE_LIFETIME = ' . $access_code_lifetime . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/countUp.min.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/access_code_timer.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Проверка кода доступа';
?>

<header class = "page-header">
	<h4>Проверка кода доступа</h4>
</header>

<div class = "alert alert-info clearfix">
	<p>На твой телефон был выслан код доступа. Введи его в поле ниже.</p>
	<p>
		Код будет валиден ещё
		<span id = "access-code-lifetime" data-login-url = "<?=
			$this->createUrl('site/login')
		?>">
			<?= $access_code_lifetime ?>
		</span>
		с.
	</p>
</div>

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
		'<span class = "glyphicon glyphicon-lock"></span> Проверить',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
