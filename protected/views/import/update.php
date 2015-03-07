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

	$this->pageTitle =
		Yii::app()->name
		. ' - Изменить импорт за '
		. $model->getFormattedDate();
?>

<header class = "page-header clearfix header-with-button">
	<a
		class = "btn btn-default pull-right"
		href = "<?=
			$this->createUrl(
				'import/view',
				array('id' => $model->id)
			)
		?>"
		title = "Закрыть">
		<span class = "glyphicon glyphicon-remove"></span>
	</a>
	<button
		class = "btn btn-danger pull-right save-and-import-button"
		<?= $model->imported ? 'disabled = "disabled"' : '' ?>
		title = "Сохранить и импортировать">
		<span class = "glyphicon glyphicon-share-alt"></span>
	</button>
	<button
		class = "btn btn-primary pull-right save-import-button"
		title = "Сохранить"
		data-save-url = "<?=
			$this->createUrl(
				'import/update',
				array('id' => $model->id)
			)
		?>">
		<img
			src = "<?=
				Yii::app()->request->baseUrl
			?>/images/processing-icon.gif"
			alt = "..." />
		<span class = "glyphicon glyphicon-floppy-disk"></span>
	</button>

	<h4>
		Изменить импорт за <time><?= $model->getFormattedDate() ?></time>
		<span
			class = "label label-<?=
				$model->imported
					? 'success'
					: 'danger'
			?> import-flag"
			title = "<?=
				$model->imported
					? 'Импортированно'
					: 'Не импортированно'
			?>">
			<span
				class = "glyphicon glyphicon-<?=
					$model->imported
						? 'star'
						: 'star-empty'
				?>">
			</span>
		</span>
	</h4>
</header>

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

	<?= CHtml::hiddenField('Import[import]') ?>
<?php $this->endWidget(); ?>
