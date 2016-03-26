<?php
	/**
	 * @var DayController $this
	 * @var CArrayDataProvider $data_provider
	 * @var array $daily_stats
	 * @var int $rest_days
	 * @var int $rest_days_prefix
	 * @var string $target_date
	 * @var string $target_my_date
	 */

	$this->pageTitle = Yii::app()->name . ' - Дни';
?>

<p class = "unimportant-text italic-text without-bottom-margin">
	От текущей дюжины <?= $rest_days_prefix ?>
	<strong><?= $rest_days ?></strong>.
</p>
<p class = "unimportant-text italic-text">
	Текущая дюжина закончится <strong><time title = "<?= $target_date ?>"><?=
		$target_my_date
	?></time></strong>.
</p>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'ExtendedGridView',
		array(
			'id' => 'day-list',
			'dataProvider' => $data_provider,
			'dailyStats' => $daily_stats,
			'template' => '{items} {summary} {pager}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
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
								. 'day-completed-flag\" '
							. 'title = \""'
								. '. ($data["completed"]'
									. '? "Завершён"'
									. ': "Не завершён") . "\">'
							. '<span '
								. 'class = \"glyphicon glyphicon-"'
									. '. ($data["completed"]'
										. '? "check"'
										. ': "unchecked") . "\">'
							. '</span>'
						. '</span>"',
					'htmlOptions' => array(
						'class' => 'day-completed-flag-column'
					)
				),
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
					'htmlOptions' => array('class' => 'date-column')
				),
				array(
					'type' => 'raw',
					'value' =>
						'"<span '
							. 'class = \"unimportant-text italic-text\""'
							. '. ($this->grid->owner->findSatisfiedCounter('
									. '$this->grid->dailyStats,'
									. '$data'
								. ') != -1'
								. '? " title = \"Выполнено\""'
								. ': "") . ">"'
							. '. $this->grid->owner->formatSatisfiedCounter('
								. '$this->grid->owner->findSatisfiedCounter('
									. '$this->grid->dailyStats,'
									. '$data'
								. ')'
							. ')'
						. '. "</span>"',
					'htmlOptions' => array('class' => 'day-satisfied-column')
				),
				array(
					'type' => 'raw',
					'value' =>
						'"<span class = \"unimportant-text italic-text\">"'
							. '. $data["daily"]'
							. '. "+"'
							. '. PointFormatter::formatNumberOfPoints('
								. '$data["projects"]'
							. ')'
						. '. "</span>"'
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
									. '"day/update",'
									. 'array("date" => $data["date"])'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Изменить день')
						)
					)
				)
			),
			'itemsCssClass' => 'table table-striped',
			'loadingCssClass' => 'wait',
			'rowCssClassExpression' =>
				'$this->controller->getRowClass($data["date"])',
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
