<?php
	/* @var $this ImportController */
	/* @var $model Import */

	$this->pageTitle =
		Yii::app()->name
		. ' - Импорт за '
		. $model->getFormattedDate();
?>

<header class = "page-header clearfix header-with-button">
	<a
		class = "btn btn-default pull-right"
		href = "<?=
			$this->createUrl('import/update', array('id' => $model->id))
		?>">
		<span class = "glyphicon glyphicon-pencil"></span>
		Изменить импорт
	</a>
	<h4>
		Импорт за <time><?= $model->getFormattedDate() ?></time>
		<span
			class = "label label-<?=
			$model->imported
				? 'success'
				: 'danger'
			?>">
			<?= $model->imported ? 'Imported' : 'Not imported' ?>
		</span>
	</h4>
</header>

<article class = "import-view">
	<pre><?= $model->points_description ?></pre>
</article>
