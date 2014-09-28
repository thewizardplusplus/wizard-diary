<?php
	/**
	 * @var AccessController $this
	 * @var CActiveDataProvider $data_provider
	 * @var array $counts
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/countries_codes.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/ajax_error_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/access_data_loader.js'),
		CClientScript::POS_HEAD
	);

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
					'name' => 'ip',
					'htmlOptions' => array('class' => 'access-ip')
				),
				array(
					'header' => 'User-Agent',
					'name' => 'user_agent',
					'htmlOptions' => array('class' => 'access-user-agent')
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
			/*'rowCssClassExpression' =>
				'"access-data"'
					. '. ($counts[$data->ip]'
						. '> Constants::LOGIN_LIMIT_MAXIMAL_COUNT'
						. '? "danger"'
						. ': "")',*/
			'rowHtmlOptionsExpression' => 'array('
				. '"class" => "access-data",'
				. '"data-ip" => CHtml::encode($data->ip),'
				. '"data-decode-ip-url" =>'
					. '$this->controller->createUrl('
						. '"access/decodeIp",'
						. 'array('
							. '"ip" => rawurlencode($data->ip)'
						. ')'
					. '),'
				. '"data-user-agent" => CHtml::encode($data->user_agent),'
				. '"data-decode-user-agent-url" =>'
					. '$this->controller->createUrl('
						. '"access/decodeUserAgent",'
						. 'array('
							. '"user_agent" => rawurlencode($data->user_agent)'
						. ')'
					. ')'
			. ')',
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'afterAjaxUpdate' => 'function() { AccessData.load(); }',
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
