<?php
	/**
	 * @var ParametersController $this
	 * @var ParametersForm $model
	 * @var string $start_date_container_class
	 * @var string $password_copy_container_class
	 * @var CActiveForm $form
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/datepicker.min.js'),
		CClientScript::POS_HEAD
	);

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

	<div
		class = "form-group start-date-input-container<?= $start_date_container_class ?>">
		<?= $form->labelEx(
			$model,
			'start_date',
			array('class' => 'control-label')
		) ?>
		<div class = "input-group">
			<?php $this->widget(
				'zii.widgets.jui.CJuiDatePicker',
				array(
					'model' => $model,
					'attribute' => 'start_date',
					'language' => 'ru',
					'options' => array(
						'showOn' => 'button',
						'showButtonPanel' => true,
						'duration' => 0,
						'showAnimType' => '',
						'beforeShow' => new CJavaScriptExpression(
							'DataPicker.onShow'
						),
						'onClose' => new CJavaScriptExpression(
							'DataPicker.onHide'
						)
					),
					'cssFile' => 'jquery-ui.min.css',
					'theme' => 'start',
					'themeUrl' =>
						Yii::app()->request->baseUrl
						. '/jquery-ui-theme',
					'htmlOptions' => array('class' => 'form-control')
				)
			); ?>
			<a class = "input-group-addon datapicker-show-button" href = "#">
				<span class = "glyphicon glyphicon-calendar"></span>
			</a>
		</div>
		<?= $form->error(
			$model,
			'start_date',
			array('inputContainer' => '.start-date-input-container')
		) ?>
	</div>

	<fieldset>
		<legend>Пароль:</legend>

		<div class = "form-group">
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

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-floppy-disk"></span> Сохранить',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
