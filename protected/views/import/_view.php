<?php
	/* @var $this ImportController */
	/* @var $data Import */
?>

<?php if ($this->action->id == 'view') { ?>
	<header class = "page-header">
		<h4>
			Импорт за <time><?= $data->date ?></time>
			<span class="label label-<?= $data->imported ? 'success' : 'danger' ?>">
				<?= $data->imported ? 'Imported' : 'Not imported' ?>
			</span>
		</h4>
	</header>
<?php } ?>

<article>
	<?php if ($this->action->id != 'view') { ?>
		<h4>
			<?= CHtml::link(
				'<time>' . $data->date . '</time>',
				$this->createUrl('import/view', array('id' => $data->id))
			) ?>
			<span class="label label-<?= $data->imported ? 'success' : 'danger' ?>">
				<?= $data->imported ? 'Imported' : 'Not imported' ?>
			</span>
		</h4>
	<?php } ?>

	<?php if ($this->action->id == 'view') { ?>
		<pre><?= $data->points_description ?></pre>
	<?php } ?>
</article>
