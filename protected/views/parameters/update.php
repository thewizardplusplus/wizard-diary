<?php
	/**
	 * @var ParametersController $this
	 * @var ParametersForm $model
	 * @var string $password_container_class
	 * @var string $password_copy_container_class
	 * @var string $session_lifetime_container_class
	 * @var string $access_log_lifetime_container_class
	 * @var CActiveForm $form
	 */

	Yii::app()->getClientScript()->registerPackage('jquery.ui');
	Yii::app()->getClientScript()->registerPackage(
		'awesome-bootstrap-checkbox'
	);

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/parameters_form.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Параметры';
?>

<header class = "page-header">
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

	<div class = "panel panel-default">
		<fieldset>
			<legend>Пароль</legend>

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
		</fieldset>
	</div>

	<div class = "form-group<?= $session_lifetime_container_class ?>">
		<?= $form->labelEx(
			$model,
			'session_lifetime_in_min',
			array(
				'class' => 'control-label',
				'label' => sprintf(
					'%s (от %d до %d)',
					$model->getAttributeLabel('session_lifetime_in_min'),
					Constants::SESSION_LIFETIME_IN_MIN_MINIMUM,
					Constants::SESSION_LIFETIME_IN_MIN_MAXIMUM
				)
			)
		) ?>
		<?= $form->textField(
			$model,
			'session_lifetime_in_min',
			array(
				'class' => 'form-control',
				'autocomplete' => 'off',
				'min' => Constants::SESSION_LIFETIME_IN_MIN_MINIMUM,
				'max' => Constants::SESSION_LIFETIME_IN_MIN_MAXIMUM
			)
		) ?>
		<?= $form->error($model, 'session_lifetime_in_min') ?>
	</div>

	<div class = "form-group<?= $access_log_lifetime_container_class ?>">
		<?= $form->labelEx(
			$model,
			'access_log_lifetime_in_s',
			array(
				'class' => 'control-label',
				'label' => sprintf(
					'%s (от %d до %d, 0 &mdash; вечность)',
					$model->getAttributeLabel('access_log_lifetime_in_s'),
					Constants::ACCESS_LOG_LIFETIME_IN_S_MINIMUM,
					Constants::ACCESS_LOG_LIFETIME_IN_S_MAXIMUM
				)
			)
		) ?>
		<?= $form->textField(
			$model,
			'access_log_lifetime_in_s',
			array(
				'class' => 'form-control',
				'autocomplete' => 'off',
				'min' => Constants::ACCESS_LOG_LIFETIME_IN_S_MINIMUM,
				'max' => Constants::ACCESS_LOG_LIFETIME_IN_S_MAXIMUM
			)
		) ?>
		<?= $form->error($model, 'access_log_lifetime_in_s') ?>
	</div>

	<div class = "checkbox checkbox-primary">
		<?= $form->checkBox($model, 'use_whitelist') ?>
		<?= $form->labelEx($model, 'use_whitelist') ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-floppy-disk"></span> Сохранить',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
