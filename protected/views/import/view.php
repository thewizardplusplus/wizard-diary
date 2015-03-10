<?php
	/* @var $this ImportController */
	/* @var $model Import */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import_view.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle =
		Yii::app()->name
		. ' - Импорт за '
		. DateFormatter::formatMyDate($model->date);
?>

<header class = "page-header clearfix header-with-button">
	<button
		class = "btn btn-default pull-right view-button import-button"
		<?= $model->imported ? 'disabled = "disabled"' : '' ?>
		title = "Импортировать"
		data-import-url = "<?=
			$this->createUrl('import/import', array('id' => $model->id))
		?>"
		data-date = "<?= DateFormatter::formatMyDate($model->date) ?>">
		<span class = "glyphicon glyphicon-share-alt"></span>
	</button>
	<a
		class = "btn btn-default pull-right view-button"
		href = "<?=
			$this->createUrl('import/update', array('id' => $model->id))
		?>"
		title = "Изменить">
		<span class = "glyphicon glyphicon-pencil"></span>
	</a>

	<h4>
		Импорт за <time><?= DateFormatter::formatMyDate($model->date) ?></time>
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
	<p class = "unimportant-text"><?= $model->getNumberOfPoints() ?></p>
</header>

<article class = "import-view">
	<pre><?= $model->getFormattedPointsDescription() ?></pre>
</article>

<?php $this->renderPartial('_import_dialog'); ?>
