<?php
	/* @var $this PointController */
	/* @var $model Point */
	/* @var $dataProvider CActiveDataProvider */

	$this->pageTitle = Yii::app()->name;

	$this->renderPartial('_form', array('model' => $model));
?>

<div class = "table-responsive">
	<?php
		$this->widget('ext.groupgridview.GroupGridView', array(
			'id' => 'point_list',
			'dataProvider' => $dataProvider,
			'template' => '{items} {pager}',
			'hideHeader' => TRUE,
			'selectableRows' => 0,
			'columns' => array(
				array(
					'class' => 'PointStateColumn',
					'htmlOptions' => array('style' => 'width: 45px; text-align:'
						. ' center;')
				),
				array(
					'type' => 'html',
					'value' => '"<span class = \"state-" . strtolower(' .
						'str_replace("_", "-", $data->state)) . "\">" . $data->'
						. 'text . "</span>"'
				),
				array(
					'class' => 'CButtonColumn',
					'buttons' => array(
						'view' => array('visible' => 'FALSE'),
						'update' => array(
							'label' => '<span class = ' .
								'"glyphicon glyphicon-pencil"></span>',
							'imageUrl' => FALSE,
							'options' => array(
								'title' => 'Изменить пункт',
								'style' => 'font-size: larger;'
							),
							'visible' => '!empty($data->text)'
						),
						'delete' => array(
							'label' => '<span class = ' .
								'"glyphicon glyphicon-trash"></span>',
							'imageUrl' => FALSE,
							'options' => array(
								'title' => 'Удалить пункт',
								'style' => 'font-size: larger;'
							)
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'pager' => array(
				'maxButtonCount' => PointController::
					MAXIMUM_PAGINATION_BUTTON_COUNT,
				'header' => '',
				'firstPageLabel' => '&lt;&lt;',
				'prevPageLabel' => '&lt;',
				'nextPageLabel' => '&gt;',
				'lastPageLabel' => '&gt;&gt;',
				'hiddenPageCssClass' => 'disabled',
				'selectedPageCssClass' => 'active',
				'htmlOptions' => array('class' => 'pagination')
			),
			'extraRowColumns' => array('date'),
			'extraRowExpression' => '"<h2 style = \"font-size: 24px;\"><span ' .
				'class = \"label label-success\">" . implode(".", ' .
				'array_reverse(explode("-", $data->date))) . ":</span></h2>"',
			'extraRowPos' => 'above'
		));
	?>
</div>
