<?php
	/**
	 * @var DayController $this
	 * @var CArrayDataProvider $data_provider
	 * @var string $my_date
	 * @var string $date
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Пункты за ' . $my_date;
?>

<header class = "page-header">
	<h4>
		Пункты за <span title = "<?= $date ?>"><?= $my_date ?></span>
	</h4>
</header>

<div class = "table-responsive">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'point-list',
			'dataProvider' => $data_provider,
			'template' => '{items}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array('class' => 'PointStateColumn'),
				array(
					'type' => 'raw',
					'value' =>
						'"<span '
							. 'class = '
								. '\"state-" . $data->getStateClass()'
								. '. " point-text\">"'
							. '. $data->getFormattedText() .'
						. '"</span>"'
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'rowCssClassExpression' => '$data->getRowClassByState()',
			'afterAjaxUpdate' => 'function() { PointList.afterUpdate(); }',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет пунктов.'
		)
	); ?>
</div>
