<?php
	/**
	 * @var StatsController $this
	 * @var CArrayDataProvider $data_provider
	 */

	$this->pageTitle = Yii::app()->name
		. ' - Статистика: список ежедневных пунктов';
?>

<header class = "page-header">
	<h4>Статистика: список ежедневных пунктов</h4>
</header>

<div class = "table-responsive">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' => 'PointFormatter::formatPointText($data["text"])'
				)
			),
			'itemsCssClass' => 'table table-striped',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет ежедневных пунктов.',
			'summaryText' => 'Ежедневные пункты {start}-{end} из {count}.',
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
