<?php
	/* @var $this ImportController */
	/* @var $data_provider CActiveDataProvider */

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
									. ': "danger") . "\">"'
							. '. ($data->imported'
								. '? "Imported"'
								. ': "Not imported")'
						. '. "</span>"',
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
							. '<time>" . $data->getFormattedDate() . "</time>'
						. '</a>"'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{update}',
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
						)
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