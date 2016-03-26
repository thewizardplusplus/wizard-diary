<?php
	/**
	 * @var AccessController $this
	 * @var CActiveDataProvider $data_provider
	 * @var array $counts
	 */

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var ACCESS_LOG_UPDATE_PAUSE_IN_S = '
			. Constants::ACCESS_LOG_UPDATE_PAUSE_IN_S
			. ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/countries_codes.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/access_data_loader.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/access_log_updater.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/access_info_loader.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Лог доступа';
?>

<header class = "page-header">
	<h4>Лог доступа</h4>
</header>

<div
	class = "access-totally-info-view"
	data-get-info-url = "<?= $this->createUrl('access/info') ?>">
	<p>
		Общее число запросов:
		<strong><span class = "access-counter-view">0</span></strong>.
	</p>

	<p>Скорость запросов:</p>
	<ul>
		<li>
			<strong><span class = "access-speed-by-day-view">0</span>
			в день</strong>;
		</li>
		<li>
			<strong><span class = "access-speed-by-hour-view">0</span>
			в час</strong>;
		</li>
		<li>
			<strong><span class = "access-speed-by-minute-view">0</span>
			в минуту</strong>.
		</li>
	</ul>
</div>

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
					'header' => 'IP*',
					'name' => 'ip',
					'htmlOptions' => array('class' => 'access-ip')
				),
				array(
					'header' => 'User-Agent',
					'name' => 'user_agent',
					'htmlOptions' => array('class' => 'access-user-agent')
				),
				array(
					'header' => 'Время последнего запроса',
					'value' =>
						'"<time>"'
							. '. $data->getFormattedTimestamp()'
						. '. "</time>"',
					'type' => 'raw'
				),
				array(
					'header' => 'Количество запросов',
					'name' => 'number'
				)
			),
			'rowCssClassExpression' =>
				'"access-data"'
				. '. ($data->banned ? " danger" : "")',
			'rowHtmlOptionsExpression' => 'array('
				. '"data-ip" => CHtml::encode($data->ip),'
				. '"data-decode-ip-url" =>'
					. '$this->controller->createUrl("access/decodeIp"),'
				. '"data-user-agent" => CHtml::encode($data->user_agent),'
				. '"data-decode-user-agent-url" =>'
					. '$this->controller->createUrl("access/decodeUserAgent")'
			. ')',
			'itemsCssClass' => 'table table-striped',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'afterAjaxUpdate' => 'function() { AccessData.load(); }',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет записей о доступе.',
			'summaryText' => 'Записи о доступе {start}-{end} из {count}.',
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

<p class = "small-text access-log-legend">
	* Красным отмечены запросы, IP которых были забанены.
</p>
