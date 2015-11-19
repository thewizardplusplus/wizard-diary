<?php
	/**
	 * @var StatsController $this
	 * @var CArrayDataProvider $future_achievements_provider
	 */

	$this->pageTitle = Yii::app()->name . ' - Статистика: будущие достижения';
?>

<header class = "page-header">
	<h4>Статистика: будущие достижения</h4>
</header>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.CListView',
		array(
			'id' => 'future-achievements-list',
			'dataProvider' => $future_achievements_provider,
			'itemView' => '_future_achievements_view',
			'template' => '{items} {summary} {pager}',
			'enableHistory' => true,
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет будущих достижений.',
			'summaryText' => 'Достижения {start}-{end} из {count}.',
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
