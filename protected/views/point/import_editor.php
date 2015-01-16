<?php
	/**
	 * @var PointController $this
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		Yii::app()->request->baseUrl . '/scripts/ace/ace.js',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import-editor.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Импорт пунктов';
?>

<header class = "page-header visible-xs">
	<h4>Импорт пунктов</h4>
</header>

<?= CHtml::beginForm('', 'post', array('class' => 'import-form')) ?>
	<div class = "form-group">
		<?= CHtml::label(
			'Описание пунктов',
			'points-description',
			array('class' => 'control-label')
		) ?>
		<div id = "import-editor"></div>
		<?php echo CHtml::hiddenField('points-description'); ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-upload"></span> Импорт',
		array(
			'class' => 'btn btn-primary',
			'type' => 'submit'
		)
	) ?>
<?= CHtml::endForm() ?>
