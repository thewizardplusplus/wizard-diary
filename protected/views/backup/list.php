<?php
	/* @var $this BackupController */
	/* @var $data_provider CActiveDataProvider */

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/backuping.js'), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name . ' - Бекапы';
?>

<div class = "panel panel-default">
	<p>
		<a href = "<?php echo $this->createUrl('backup/create'); ?>"><button
			class = "btn btn-primary pull-right create-backup-button"
			data-create-backup-url = "<?php echo $this->createUrl('backup/' .
				'create'); ?>">Создать новый бекап</button></a>
	</p>
	<div class = "clearfix"></div>
</div>

<div class = "table-responsive">
	<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'backup-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {pager}',
			'selectableRows' => 0,
			'columns' => array(
				array(
					'name' => 'Время создания',
					'type' => 'raw',
					'value' => '"<time>" . $data->timestamp . "</time>"'
				),
				array(
					'class' => 'CButtonColumn',
					'header' => 'Скачать',
					'template' => '{download}',
					'buttons' => array(
						'download' => array(
							'label' => '<span class = "glyphicon glyphicon-'
								. 'download-alt"></span>',
							'url' => '$data->link',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Скачать')
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'pager' => array(
				'header' => '',
				'firstPageLabel' => '&lt;&lt;',
				'prevPageLabel' => '&lt;',
				'nextPageLabel' => '&gt;',
				'lastPageLabel' => '&gt;&gt;',
				'selectedPageCssClass' => 'active',
				'hiddenPageCssClass' => 'disabled',
				'htmlOptions' => array('class' => 'pagination')
			)
		));
	?>
</div>
