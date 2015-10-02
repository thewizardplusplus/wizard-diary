<?php
	/**
	 * @var DayController $this
	 * @var CArrayDataProvider $data_provider
	 */

	$this->pageTitle = Yii::app()->name . ' - Дни';
?>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'day-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' =>
						'"<a '
							. 'href = \""'
								. '. $this->grid->controller->createUrl('
									. '"day/view",'
									. 'array("date" => $data["date"])'
								. ') . "\">'
							. '<time '
								. 'title = \""'
									. ' . DateFormatter::formatDate('
										. '$data["date"]'
									. ') . "\">"'
								. ' . DateFormatter::formatMyDate('
									. '$data["date"]'
								. ')'
							. ' . "</time>'
						. '</a>"',
					'htmlOptions' => array('class' => 'day-date-column')
				),
				array(
					'type' => 'raw',
					'value' =>
						'"<span '
							. 'class = \"'
								. 'label '
								. 'label-"'
									. '. ($data["completed"]'
										. '? "success"'
										. ': "primary") . " '
								. 'day-completed-flag\">"'
							. '. ($data["completed"]'
								. '? "Завершён"'
								. ': "Не завершён")'
						. '. "</span>"',
					'htmlOptions' => array(
						'class' => 'day-completed-flag-column'
					)
				),
				array(
					'type' => 'raw',
					'value' =>
						'"<span class = \"unimportant-text italic-text\">"'
							. '. PointFormatter::formatNumberOfPoints('
								. '$data["projects"]'
							. ')'
						. '. "</span>"'
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет пунктов.',
			'summaryText' => 'Дни {start}-{end} из {count}.',
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
