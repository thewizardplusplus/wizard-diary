<?php
	/* @var $this PointController */
	/* @var $model Point */
	/* @var $data_provider CActiveDataProvider */

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/checking.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/jquery.jeditable.min.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/editing.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/purl.min.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/move.js'), CClientScript::POS_HEAD);

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
					'type' => 'raw',
					'value' => '!empty($data->text) ? "<span id = \"point-' .
						'text-" . $data->id . "\" class = \"state-" . ' .
						'strtolower(str_replace("_", "-", $data->state)) . " ' .
						'point-text\" data-update-url = \"" . $this->grid->' .
						'controller->createUrl("point/update", array("id" => ' .
						'$data->id)) . "\" data-saving-icon-url = \"" . Yii::' .
						'app()->request->baseUrl . "/images/saving-icon.gif\">"'
						. ' . $data->text . "</span>" : ""'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{down} {up}',
					'buttons' => array(
						'down' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'arrow-down"></span>',
							'url' => '$this->grid->controller->createUrl("point'
								. '/update", array("id" => $data->id, "order" '
								. '=> $data->order + 3))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Опустить'),
							'click' => 'function() { return move($(this).' .
								'attr("href")); }'
						),
						'up' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'arrow-up"></span>',
							'url' => '$this->grid->controller->createUrl("point'
								. '/update", array("id" => $data->id, "order" '
								. '=> $data->order - 3))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Поднять'),
							'click' => 'function() { return move($(this).' .
								'attr("href")); }'
						)
					)
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{update} {delete}',
					'deleteConfirmation' => 'Удалить пункт?',
					'buttons' => array(
						'update' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'pencil"></span>',
							'url' => '"?r=point/update&id=" . $data->id',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Изменить пункт'),
							'click' => 'function() { return editing(this); }',
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
				'lastPageCssClass' => 'hidden',
				'hiddenPageCssClass' => 'disabled',
				'htmlOptions' => array('class' => 'pagination')
			),
			'extraRowColumns' => array('date'),
			'extraRowExpression' => '"<h2 class = \"reduced\"><span class = ' .
				'\"label label-success\">" . $data->getMyDate() . ":</span>' .
				'</h2>"',
			'extraRowPos' => 'above',
			'afterAjaxUpdate' => 'function() { initializeEditors(); }'
		));
	?>
</div>
