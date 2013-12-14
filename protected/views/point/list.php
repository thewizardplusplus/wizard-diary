<?php
	/* @var $this PointController */
	/* @var $model Point */
	/* @var $data_provider CActiveDataProvider */

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/checking.js'), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name;

	$this->renderPartial('_form', array('model' => $model));
?>

<div class = "table-responsive">
	<?php
		$this->widget('ext.groupgridview.GroupGridView', array(
			'id' => 'point_list',
			'dataProvider' => $data_provider,
			'template' => '{pager} {items} {pager}',
			'hideHeader' => TRUE,
			'selectableRows' => 0,
			'columns' => array(
				array(
					'class' => 'CButtonColumn',
					'template' => '{check} {uncheck}',
					'buttons' => array(
						'check' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'unchecked"></span>',
							'url' => '$this->grid->controller->createUrl("point'
								. '/update", array("id" => $data->id))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Отметить пункт'),
							'click' => 'function() { return checking($(this).' .
								'attr("href"), true); }',
							'visible' => '!empty($data->text) and !$data->check'
						),
						'uncheck' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'check"></span>',
							'url' => '$this->grid->controller->createUrl("point'
								. '/update", array("id" => $data->id))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Снять отметку с ' .
								'пункта'),
							'click' => 'function() { return checking($(this).' .
								'attr("href"), false); }',
							'visible' => '!empty($data->text) and $data->check'
						)
					),
					'htmlOptions' => array('class' => 'button-column narrow')
				),
				array('class' => 'PointStateColumn'),
				array(
					'type' => 'html',
					'value' => '"<span class = \"state-" . strtolower(' .
						'str_replace("_", "-", $data->state)) . "\">" . $data->'
						. 'text . "</span>"'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{update} {delete}',
					'deleteConfirmation' => 'Удалить пункт?',
					'buttons' => array(
						'update' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'pencil"></span>',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Изменить пункт'),
							'visible' => '!empty($data->text)'
						),
						'delete' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'trash"></span>',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Удалить пункт')
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'rowCssClassExpression' => '$data->getRowClassByState()',
			'pager' => array(
				'maxButtonCount' => 0,
				'header' => '',
				'prevPageLabel' => '&lt;',
				'nextPageLabel' => '&gt;',
				'lastPageLabel' => '&gt;&gt;',
				'firstPageCssClass' => 'hidden',
				'hiddenPageCssClass' => 'disabled',
				'htmlOptions' => array('class' => 'pagination')
			),
			'extraRowColumns' => array('date'),
			'extraRowExpression' => '"<h2 class = \"reduced\"><span class = ' .
				'\"label label-success\">" . implode(".", array_reverse(' .
				'explode("-", $data->date))) . ":</span></h2>"',
			'extraRowPos' => 'above'
		));
	?>
</div>
