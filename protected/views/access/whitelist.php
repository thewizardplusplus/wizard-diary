<?php
	/**
	 * @var AccessController $this
	 * @var CActiveDataProvider $data_provider
	 */

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
			'itemsCssClass' => 'table table-striped',
			'emptyText' => 'Нет записей.'
		)
	); ?>
</div>
