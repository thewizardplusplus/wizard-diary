<?php
	/**
	 * @var PointController $this
	 * @var Point $model
	 * @var CActiveDataProvider $data_provider
	 * @var int $number_of_pages
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/jquery.jeditable.min.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var NUMBER_OF_PAGES = ' . $number_of_pages . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/purl.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/correcting_url.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/deleting_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/ajax_error_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_list.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/selection.js'),
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
			'hideHeader' => true,
			'selectableRows' => 2,
			'enableHistory' => true,
			'columns' => array(
				array('class' => 'PointStateColumn'),
				array(
					'type' => 'raw',
					'value' =>
						'"<span '
							. 'id = \"point-text-" . $data->id . "\" '
							. 'class = '
								. '\"state-" . $data->getStateClass()'
								. '. " point-text\" '
							. 'data-text = '
								. '\"" . $data->getRealText() . "\" '
							. 'data-update-url = '
								. '\"" . $this->grid->controller->createUrl('
									. '"point/update",'
									. 'array("id" => $data->id)'
								. ') . "\" '
							. 'data-saving-icon-url = '
								. '\"" . Yii::app()->request->baseUrl'
								. '. "/images/processing-icon.gif\">"'
							. '. $data->getFormattedText() .'
						. '"</span>"'
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
							'imageUrl' => false,
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
							'imageUrl' => false,
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
			'afterAjaxUpdate' => 'function() { PointList.afterUpdate(); }',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'selectionChanged' => 'function() { Selection.process(); }',
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
					. '<span '
						. 'class = \"label label-success\"'
						. 'title = \""'
							. ' . DateFormatter::formatDate($data->date)'
						. ' . "\">"'
						. ' . DateFormatter::formatMyDate($data->date) . '
					. '":</span>'
				. '</span>"',
			'extraRowPos' => 'above'
		)
	); ?>
</div>

<div class = "modal deleting-dialog">
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
				<h4 class = "modal-title">
					<span class = "glyphicon glyphicon-warning-sign"></span>
					Внимание!
				</h4>
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
