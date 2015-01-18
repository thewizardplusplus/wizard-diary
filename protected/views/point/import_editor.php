<?php
	/**
	 * @var PointController $this
	 */

	Yii::app()->getClientScript()->registerPackage('jquery.ui');
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
			'Целевая дата',
			'target-date',
			array('class' => 'control-label')
		) ?>
		<?php
			$this->widget(
				'zii.widgets.jui.CJuiDatePicker',
				array(
					'name' => 'target-date',
					'value' => date('Y-m-d'),
					'options' => array(
						'showOtherMonths' => true,
						'selectOtherMonths' => true,
						'showButtonPanel' => true,
						'dateFormat' => 'yy-mm-dd',
						'beforeShow' => new CJavaScriptExpression(
							'function (input, instance) {'
								. 'instance.dpDiv.css('
									. '{'
										. 'marginLeft: ($(input).outerWidth() -'
											. 'instance.dpDiv.outerWidth()) +'
											. '"px"'
									. '}'
								. ');'
							. '}'
						)
					),
					'theme' => 'start',
					'htmlOptions' => array(
						'class' => 'form-control'
					)
				)
			);
		?>
	</div>

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
