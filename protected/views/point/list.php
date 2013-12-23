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
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/addition.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/deleting.js'), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name;
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
					'value' => '"<span id = \"point-text-" . $data->id . "\" ' .
						'class = \"state-" . strtolower(str_replace("_", "-", '
						. '$data->state)) . " point-text\" data-update-url = ' .
						'\"" . $this->grid->controller->createUrl("point/' .
						'update", array("id" => $data->id)) . "\" data-saving-'
						. 'icon-url = \"" . Yii::app()->request->baseUrl . ' .
						'"/images/saving-icon.gif\">" . $data->text . "</span>"'
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
					'buttons' => array(
						'update' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'pencil"></span>',
							'url' => '$this->grid->controller->createUrl("point'
								. '/update", array("id" => $data->id, "_id" '
								. '=> $data->id))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Изменить пункт'),
							'click' => 'function() { return editing(this); }'
						),
						'delete' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'trash"></span>',
							'url' => '$this->grid->controller->createUrl("point'
								. '/delete", array("id" => $data->id, "_id" '
								. '=> $data->id))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Удалить пункт'),
							'click' => 'function() { return deleting(this); }'
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
				'firstPageCssClass' => 'hidden',
				'lastPageCssClass' => 'hidden',
				'hiddenPageCssClass' => 'disabled',
				'htmlOptions' => array('class' => 'pagination')
			),
			'extraRowColumns' => array('date'),
			'extraRowExpression' => '"<span class = \"date-row\"><span class = '
				. '\"label label-success\">" . $data->getMyDate() . ":</span>' .
				'</span>"',
			'extraRowPos' => 'above',
			'afterAjaxUpdate' => 'function() { initializeEditors(); }'
		));
	?>
</div>

<?php echo CHtml::beginForm(
	'#',
	'post',
	array('id' => 'point-addition-form')
); ?>
	<div class = "input-group">
		<?php echo CHtml::textField('Point_text', '', array('class' =>
			'form-control')); ?>
		<a class = "input-group-addon add-point-button" href = "<?php echo
			$this->createUrl('point/create'); ?>"><span class =
			"glyphicon glyphicon-plus"></span></a>
	</div>
<?php echo CHtml::endForm(); ?>

<div class = "modal">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button type = "button" class = "close" data-dismiss = "modal"
					aria-hidden = "true">&times;</button>
				<h4 class = "modal-title">Подтверждение</h4>
			</div>
			<div class = "modal-body">
				<p>Ты точно хочешь удалить пункт <strong>&laquo;<span class =
					"point-text"></span>&raquo;</strong>?</p>
			</div>
			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary ok-button">OK
					</button>
				<button type = "button" class = "btn btn-default" data-dismiss =
					"modal">Отмена</button>
			</div>
		</div>
	</div>
</div>
