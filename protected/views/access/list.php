<?php
	/**
	 * @var AccessController $this
	 * @var CActiveDataProvider $data_provider
	 */

	$this->pageTitle = Yii::app()->name . ' - Лог доступа';
?>

<header class = "page-header visible-xs">
	<h4>Лог доступа</h4>
</header>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'access-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'header' => 'IP',
					'name' => 'ip'
				),
				array(
					'header' => 'User-Agent',
					'name' => 'user_agent'
				),
				array(
					'header' => 'Время последнего доступа',
					'value' =>
						'"<time>"'
						. '. $data->getFormattedTimestamp()'
						. '. "</time>"',
					'type' => 'raw'
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет записей о доступе.',
			'summaryText' => 'Записи {start}-{end} из {count}.',
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
