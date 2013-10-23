<?php
	/* @var $this BackupController */
	/* @var $data_provider CActiveDataProvider */
?>

<div class = "panel panel-default">
	<p>
		<a href = "<?php echo $this->createUrl('backup/new'); ?>"><button type =
			"button" class = "btn btn-primary pull-right">Создать новый бекап
			</button></a>
	</p>
	<div class="clearfix"></div>
</div>

<div class = "table-responsive">
	<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'dataProvider' => $data_provider,
			'template' => '{items}',
			'selectableRows' => 0,
			'columns' => array(
				array(
					'name' => 'Время создания',
					'type' => 'raw',
					'value' => '"<time>" . $data["timestamp"] . "</time>"'
				),
				array(
					'name' => 'Размер',
					'value' => '$data["size"]'
				),
				array(
					'header' => 'Скачать',
					'class' => 'CLinkColumn',
					'label' => '<span class="glyphicon glyphicon-download-alt">'
						. '</span>',
					'urlExpression' => '$data["link"]',
					'htmlOptions' => array('style' => 'font-size: larger;')
				)
			),
			'itemsCssClass' => 'table'
		));
	?>
</div>
