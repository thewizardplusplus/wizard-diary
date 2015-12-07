<?php
	/**
	 * @var AccessController $this
	 * @var CActiveDataProvider $data_provider
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/countries_codes.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/access_data_loader.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Белый список';
?>

<header class = "page-header">
	<h4>Белый список</h4>
</header>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'whitelist',
			'dataProvider' => $data_provider,
			'template' => '{items}',
			'selectableRows' => 0,
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
					'header' => 'Время последней авторизации',
					'value' =>
						'"<time>"'
							. '. $data->getFormattedTimestamp()'
						. '. "</time>"',
					'type' => 'raw'
				)
			),
			'rowCssClass' => array('access-data'),
			'rowHtmlOptionsExpression' => 'array('
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
			'itemsCssClass' => 'table table-striped',
			'emptyText' => 'Нет записей.'
		)
	); ?>
</div>
