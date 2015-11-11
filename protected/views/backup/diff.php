<?php
	/**
	 * @var BackupController $this
	 * @var string $diff_representation
	 */

	Yii::app()->getClientScript()->registerPackage('ace');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/diff_viewer.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Бекапы: изменения';
?>

<header class = "page-header">
	<h4>Бекапы: изменения</h4>
</header>

<div id = "diff-viewer"><?= CHtml::encode($diff_representation) ?></div>
