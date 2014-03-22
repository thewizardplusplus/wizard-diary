<?php
	/**
	 * @var PointController $this
	 * @var Point $model
	 * @var CActiveDataProvider $data_provider
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/jquery.jeditable.min.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/purl.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name;
?>

<div class = "table-responsive">
	<?php $this->widget(
		'ext.groupgridview.GroupGridView',
		array(
			'id' => 'point-list',
			'dataProvider' => $data_provider,
			'template' => '{pager} {items} {pager}',
			'hideHeader' => TRUE,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'class' => 'CButtonColumn',
					'template' => '{check} {uncheck}',
					'buttons' => array(
						'check' => array(
							'label' =>
								'<span class = "glyphicon glyphicon-unchecked">'
									. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"point/update",'
									. 'array("id" => $data->id)'
								. ')',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Отметить пункт'),
							'click' =>
								'function() {'
									. 'return PointList.checking('
										. '$(this).attr("href"),'
										. 'true'
									. ');'
								. '}',
							'visible' => '!empty($data->text) and !$data->check'
						),
						'uncheck' => array(
							'label' =>
								'<span class = "glyphicon glyphicon-check">'
								. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"point/update",'
									. 'array("id" => $data->id)'
								. ')',
							'imageUrl' => FALSE,
							'options' => array(
								'title' => 'Снять отметку с пункта'
							),
							'click' =>
								'function() {'
									. 'return PointList.checking('
										. '$(this).attr("href"),'
										. 'false'
									. ');'
								. '}',
							'visible' => '!empty($data->text) and $data->check'
						)
					),
					'htmlOptions' => array('class' => 'button-column narrow')
				),
				array('class' => 'PointStateColumn'),
				array(
					'type' => 'raw',
					'value' =>
						'"<span '
							. 'id = \"point-text-" . $data->id . "\" '
							. 'class = '
								. '\"state-" . $data->getStateClass()'
								. '. " point-text\" '
							. 'data-update-url = '
								. '\"" . $this->grid->controller->createUrl('
									. '"point/update",'
									. 'array("id" => $data->id)'
								. ') . "\" '
							. 'data-saving-icon-url = '
								. '\"" . Yii::app()->request->baseUrl'
								. '. "/images/processing-icon.gif\">"'
							. '. $data->text .'
						. '"</span>"'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{down} {up}',
					'buttons' => array(
						'down' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-arrow-down">'
								. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"point/update",'
									. 'array('
										. '"id" => $data->id,'
										. '"order" => $data->order + 3'
									. ')'
								. ')',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Опустить'),
							'click' =>
								'function() {'
									. 'return PointList.move('
										. '$(this).attr("href")'
									. ');'
								. '}'
						),
						'up' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-arrow-up">'
								. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"point/update",'
									. 'array('
										. '"id" => $data->id,'
										. '"order" => $data->order - 3'
									. ')'
								. ')',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Поднять'),
							'click' =>
								'function() {'
									. 'return PointList.move('
										. '$(this).attr("href")'
									. ');'
								. '}'
						),
					)
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{update} {delete}',
					'buttons' => array(
						'update' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-pencil">'
								. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"point/update",'
									. 'array('
										. '"id" => $data->id,'
										. '"_id" => $data->id'
									. ')'
								. ')',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Изменить пункт'),
							'click' =>
								'function() {'
									. 'return PointList.editing(this);'
								. '}'
						),
						'delete' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-trash">'
								. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"point/delete",'
									. 'array('
										. '"id" => $data->id,'
										. '"_id" => $data->id'
									. ')'
								. ')',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Удалить пункт'),
							'click' =>
								'function() {'
									. 'return PointList.deleting(this);'
								. '}'
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'rowCssClassExpression' => '$data->getRowClassByState()',
			'afterAjaxUpdate' => 'function() { PointList.initialize(); }',
			'emptyText' => 'Нет пунктов.',
			'pager' => array(
				'maxButtonCount' => 0,
				'header' => '',
				'prevPageLabel' => '&lt;',
				'nextPageLabel' => '&gt;',
				'firstPageCssClass' => 'hidden',
				'lastPageCssClass' => 'hidden',
				'hiddenPageCssClass' => 'disabled',
				'htmlOptions' => array('class' => 'pager')
			),
			'pagerCssClass' => 'page-controller',
			'extraRowColumns' => array('date'),
			'extraRowExpression' =>
				'"<span class = \"date-row\">'
				. '<span class = \"label label-success\">"'
				. ' . $data->getMyDate() . '
				. '":</span>'
				. '</span>"',
			'extraRowPos' => 'above'
		)
	); ?>
</div>

<?= CHtml::beginForm('#', 'post', array('id' => 'point-addition-form')) ?>
	<div class = "input-group">
		<?= CHtml::textField(
			'Point_text',
			'',
			array('class' => 'form-control')
		) ?>
		<a
			class = "input-group-addon add-point-button"
			href = "<?= $this->createUrl('point/create') ?>">
			<span class = "glyphicon glyphicon-plus"></span>
		</a>
	</div>
<?= CHtml::endForm() ?>

<div class = "modal">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button
					class = "close"
					type = "button"
					data-dismiss = "modal"
					aria-hidden = "true">
					&times;
				</button>
				<h4 class = "modal-title">Подтверждение</h4>
			</div>
			<div class = "modal-body">
				<p>
					Ты точно хочешь удалить
					<span class = "point-description"></span>?
				</p>
			</div>
			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary ok-button">
					OK
				</button>
				<button
					class = "btn btn-default"
					type = "button"
					data-dismiss = "modal">
					Отмена
				</button>
			</div>
		</div>
	</div>
</div>
