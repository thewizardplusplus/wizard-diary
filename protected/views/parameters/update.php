<?php
	/**
	 * @var ParametersController $this
	 * @var ParametersForm $model
	 * @var string $password_container_class
	 * @var string $password_copy_container_class
	 * @var string $points_on_page_container_class
	 * @var string $access_log_lifetime_container_class
	 * @var CActiveForm $form
	 */

	Yii::app()->getClientScript()->registerPackage('jquery.ui');
	Yii::app()->getClientScript()->registerCssFile(
		CHtml::asset('styles/custom_spinner.css')
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/custom_spinner.js'),
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

	<div class = "form-group<?= $points_on_page_container_class ?>">
		<?= $form->labelEx(
			$model,
			'points_on_page',
			array('class' => 'control-label')
		) ?>
		<?= $form->textField(
			$model,
			'points_on_page',
			array(
				'class' => 'form-control',
				'autocomplete' => 'off',
				'min' => Constants::POINTS_ON_PAGE_MINIMUM,
				'max' => Constants::POINTS_ON_PAGE_MAXIMUM
			)
		) ?>
		<?= $form->error($model, 'points_on_page') ?>
	</div>

	<div class = "form-group<?= $access_log_lifetime_container_class ?>">
		<?= $form->labelEx(
			$model,
			'access_log_lifetime_in_s',
			array('class' => 'control-label')
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

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-floppy-disk"></span> Сохранить',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
