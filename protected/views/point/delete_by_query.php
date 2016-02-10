<?php
	/**
	 * @var PointController $this
	 * @var DeleteByQueryForm $model
	 * @var string $query_container_class
	 */

	$this->pageTitle = Yii::app()->name . ' - Удаление по запросу';
?>

<header class = "page-header">
	<h4>Удаление по запросу</h4>
</header>

<?php $form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'delete-by-query-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger',
		'clientOptions' => array(
			'errorCssClass' => 'has-error',
			'successCssClass' => 'has-success'
		),
		'htmlOptions' => array('class' => 'delete-by-query-form')
	)
); ?>
	<?= $form->errorSummary(
		$model,
		NULL,
		NULL,
		array('class' => 'alert alert-danger')
	) ?>

	<div class = "form-group<?= $query_container_class ?>">
		<?= $form->labelEx(
			$model,
			'query',
			array('class' => 'control-label')
		) ?>
		<?= $form->textField(
			$model,
			'query',
			array(
				'class' => 'form-control query-editor',
				'autocomplete' => 'off',
				'required' => 'required'
			)
		) ?>
		<?= $form->error($model, 'query') ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-trash"></span> Удалить',
		array(
			'class' => 'btn btn-primary delete-by-query-button',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>
