<?php
	/* @var $this PointController */
	/* @var $model Point */
	/* @var $data_provider CActiveDataProvider */

	$this->pageTitle = Yii::app()->name;

	$this->renderPartial('_form', array('model' => $model));
?>

<div class = "table-responsive">
	<?php
		$this->widget('ext.groupgridview.GroupGridView', array(
			'id' => 'point_list',
			'dataProvider' => $data_provider,
			'template' => '{pager} {items} {pager}',
			'hideHeader' => TRUE,
			'selectableRows' => 0,
			'columns' => array(
				array(
					'class' => 'CCheckBoxColumn',
					'id' => 'point_check',
					'checked' => '$data->check',
					'selectableRows' => 2,
					'htmlOptions' => array('style' => 'width: 45px; text-align:'
						. ' center;'),
					'checkBoxHtmlOptions' => array('onclick' => 'return ' .
						'processPointChecked(this);')
				),
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
			'loadingCssClass' => 'wait',
			'pager' => array(
				'maxButtonCount' => 0,
				'header' => '',
				'prevPageLabel' => '&lt;',
				'nextPageLabel' => '&gt;',
				'firstPageCssClass' => 'hidden',
				'lastPageCssClass' => 'hidden',
				'hiddenPageCssClass' => 'disabled',
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
