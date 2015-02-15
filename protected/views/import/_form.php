<?php
	/* @var $this ImportController */
	/* @var $model Import */
	/* @var $form CActiveForm */

	Yii::app()->getClientScript()->registerScriptFile(
		Yii::app()->request->baseUrl . '/scripts/ace/ace.js',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import-editor.js'),
		CClientScript::POS_HEAD
	);
?>

<?php
	$form = $this->beginWidget(
		'CActiveForm',
		array(
			'enableAjaxValidation' => true,
			'enableClientValidation' => true,
			'errorMessageCssClass' => 'alert alert-danger',
			'htmlOptions' => array('class' => 'import-form')
		)
	);
?>
	<?= $form->errorSummary(
		$model,
		NULL,
		NULL,
		array('class' => 'alert alert-danger')
	) ?>

	<div class = "form-group">
		<?= $form->labelEx($model, 'points_description') ?>
		<div id = "import-editor"><?=
			CHtml::encode($model->points_description)
		?></div>
		<?= CHtml::hiddenField('Import[points_description]') ?>
		<?= $form->error($model, 'points_description') ?>
	</div>

	<button
		class = "btn btn-primary"
		type = "submit">
		<span
			class = "glyphicon glyphicon-<?=
				$model->isNewRecord
					? 'plus'
					: 'floppy-disk'
			?>">
		</span>
		<?= $model->isNewRecord ? 'Создать' : 'Сохранить' ?>
	</button>
	<a
		class = "btn btn-default"
		href = "<?=
			$model->isNewRecord
				? $this->createUrl('import/list')
				: $this->createUrl(
					'import/view',
					array('id' => $model->id)
				) ?>">
		<span class = "glyphicon glyphicon-remove"></span>
		Отмена
	</a>
<?php $this->endWidget(); ?>
