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
		class = "btn btn-default pull-right view-button"
		href = "<?=
			$this->createUrl('import/import', array('id' => $model->id))
		?>"
		<?= $model->imported ? 'disabled = "disabled"' : '' ?>>
		<span class = "glyphicon glyphicon-share-alt"></span>
		Импортировать
	</a>
	<a
		class = "btn btn-default pull-right view-button"
		href = "<?=
			$this->createUrl('import/update', array('id' => $model->id))
		?>">
		<span class = "glyphicon glyphicon-pencil"></span>
		Изменить
	</a>
	<h4>
		Импорт за <time><?= $model->getFormattedDate() ?></time>
		<span
			class = "label label-<?=
				$model->imported
					? 'success'
					: 'danger'
			?>">
			<?= $model->imported ? 'Импортированно' : 'Не импортированно' ?>
		</span>
	</h4>
</header>

<article class = "import-view">
	<pre><?= $model->points_description ?></pre>
</article>
