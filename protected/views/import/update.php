<?php
	/* @var $this ImportController */
	/* @var $model Import */
	/* @var $form CActiveForm */

	Yii::app()->getClientScript()->registerScriptFile(
		Yii::app()->request->baseUrl . '/scripts/ace/ace.js',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/close_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import_editor.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle =
		Yii::app()->name
		. ' - Изменить импорт за '
		. DateFormatter::formatMyDate($model->date);
?>

<header class = "page-header clearfix header-with-button">
	<button
		class = "btn btn-default pull-right close-button"
		title = "Закрыть"
		data-date = "<?= DateFormatter::formatDate($model->date) ?>"
		data-my-date = "<?= DateFormatter::formatMyDate($model->date) ?>"
		data-view-url = "<?=
			$this->createUrl(
				'import/view',
				array('id' => $model->id)
			)
		?>">
		<span class = "glyphicon glyphicon-remove"></span>
	</button>
	<button
		class = "btn btn-danger pull-right save-and-import-button"
		<?= $model->imported ? 'disabled = "disabled"' : '' ?>
		title = "Сохранить и импортировать"
		data-date = "<?= DateFormatter::formatDate($model->date) ?>"
		data-my-date = "<?= DateFormatter::formatMyDate($model->date) ?>">
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
		Изменить импорт за <time title = "<?=
			DateFormatter::formatDate($model->date)
		?>">
			<?= DateFormatter::formatMyDate($model->date) ?>
		</time>
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
		<span
			class = "label label-success saved-flag"
			title = "Сохранено">
			<span class = "glyphicon glyphicon-floppy-saved"></span>
		</span>
	</h4>
	<p class = "unimportant-text number-of-points-view"><?=
		$model->getNumberOfPoints()
	?></p>
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
	<?= CHtml::hiddenField('Import[close]') ?>
<?php $this->endWidget(); ?>

<?php $this->renderPartial('_import_dialog'); ?>

<div class = "modal close-dialog">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button
					class = "close"
					type = "button"
					data-dismiss = "modal"
					aria-hidden = "true">
					&times;
				</button>
				<h4 class = "modal-title">
					<span class = "glyphicon glyphicon-warning-sign"></span>
					Внимание!
				</h4>
			</div>

			<div class = "modal-body">
				<p>
					Импорт за <time class = "import-date"></time> не сохранён.
					Закрытие редактора может привести к потере последних
					изменений. Так что ты хочешь сделать?
				</p>
			</div>

			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary save-button">
					<span class = "glyphicon glyphicon-floppy-disk"></span>
					Сохранить и закрыть
				</button>
				<button type = "button" class = "btn btn-danger close-button">
					<span class = "glyphicon glyphicon-remove"></span>
					Закрыть
				</button>
				<button
					class = "btn btn-default"
					type = "button"
					data-dismiss = "modal">
					Отмена
				</button>
			</div>
		</div>
	</div>
</div>
