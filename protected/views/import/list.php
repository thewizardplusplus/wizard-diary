<?php
	/* @var $this ImportController */
	/* @var $data_provider CActiveDataProvider */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/purl.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/import_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Импорт';
?>

<header class = "page-header visible-xs">
	<h4>Импорт</h4>
</header>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'import-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' =>
						'"<span '
							. 'class = \"label label-"'
								. '. ($data->imported'
									. '? "success"'
									. ': "danger") . " import-flag\" '
							. 'title = \""'
								. '. ($data->imported'
									. '? "Импортированно"'
									. ': "Не импортированно") . "\">'
							. '<span '
								. 'class = \"glyphicon glyphicon-"'
									. '. ($data->imported'
										. '? "star"'
										. ': "star-empty") . "\">'
							. '</span>'
						. '</span>"',
					'htmlOptions' => array('class' => 'import-flag-column')
				),
				array(
					'type' => 'raw',
					'value' =>
						'"<a '
							. 'href = \""'
								. '. $this->grid->controller->createUrl('
									. '"import/view",'
									. 'array("id" => $data->id)'
								. ') . "\">'
							. '<time '
								. 'title = \""'
									. ' . DateFormatter::formatDate('
										. '$data->date'
									. ') . "\">"'
								. ' . DateFormatter::formatMyDate($data->date)'
							. ' . "</time>'
						. '</a>"',
					'htmlOptions' => array('class' => 'import-date-column')
				),
				array(
					'type' => 'raw',
					'value' =>
						'"<span class = \"unimportant-text\">"'
							. '. $data->getNumberOfPoints()'
						. '. "</span>"'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{update} {import}',
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
									. '"import/update",'
									. 'array("id" => $data->id)'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Изменить импорт')
						),
						'import' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-share-alt">'
								. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"import/import",'
									. 'array('
										. '"id" => $data->id,'
										. '"date" =>'
											. 'DateFormatter::formatDate('
												.'$data->date'
											. '),'
										. '"my-date" =>'
											. 'DateFormatter::formatMyDate('
												.'$data->date'
											. ')'
									. ')'
								. ')',
							'imageUrl' => false,
							'options' => array(
								'title' => 'Импортировать импорт'
							),
							'click' =>
								'function() {'
									. 'return ImportList.import(this);'
								. '}',
							'visible' => '!$data->imported'
						),
					)
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет импорта.',
			'summaryText' => 'Импорт {start}-{end} из {count}.',
			'pager' => array(
				'header' => '',
				'firstPageLabel' => '&lt;&lt;',
				'prevPageLabel' => '&lt;',
				'nextPageLabel' => '&gt;',
				'lastPageLabel' => '&gt;&gt;',
				'selectedPageCssClass' => 'active',
				'hiddenPageCssClass' => 'disabled',
				'htmlOptions' => array('class' => 'pagination')
			),
			'pagerCssClass' => 'page-controller'
		)
	); ?>
</div>

<?php $this->renderPartial('_import_dialog'); ?>
