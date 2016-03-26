<?php
	/**
	 * @var BackupController $this
	 * @var string $diff_representation
	 * @var string $previous_file_timestamp
	 * @var string $file_timestamp
	 */

	Yii::app()->getClientScript()->registerPackage('ace');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/diff_viewer.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle =
		Yii::app()->name
		. ' - Бекапы:'
		. (is_null($file_timestamp) ? ' текущие ' : ' ')
		. 'изменения';
?>

<header class = "page-header">
	<h4>Бекапы: <?= is_null($file_timestamp) ? 'текущие' : '' ?> изменения</h4>
</header>

<p>
	Изменения с момента
	<strong><time><?= $previous_file_timestamp ?></time></strong>
	к
	<?php if (!is_null($file_timestamp)) { ?>
		моменту <strong><time><?= $file_timestamp ?></time></strong>:
	<?php } else { ?>
		текущему моменту:
	<?php } ?>
</p>

<div id = "diff-viewer"><?= CHtml::encode($diff_representation) ?></div>
