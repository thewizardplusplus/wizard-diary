<?php
	/**
	 * @var BackupController $this
	 * @var CArrayDataProvider $data_provider
	 */

	$this->pageTitle = Yii::app()->name . ' - Бекапы';
?>

<header class = "page-header visible-xs">
	<h4>Бекапы</h4>
</header>

<div class = "table-responsive">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'backup-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {pager}',
			'selectableRows' => 0,
			'columns' => array(
				array(
					'name' => 'Время создания',
					'type' => 'raw',
					'value' => '$data->formatted_timestamp'
				),
				array(
					'class' => 'CButtonColumn',
					'header' => 'Скачать',
					'template' => '{download}',
					'buttons' => array(
						'download' => array(
							'label' =>
								'<span class = "glyphicon glyphicon-'
								. 'download-alt"></span>',
							'url' => '$data->link',
							'imageUrl' => false,
							'options' => array('title' => 'Скачать')
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет бекапов.',
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
