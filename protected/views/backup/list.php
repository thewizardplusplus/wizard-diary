<?php
	/**
	 * @var BackupController $this
	 * @var CArrayDataProvider $data_provider
	 */

	$this->pageTitle = Yii::app()->name . ' - Бекапы';
?>

<header class = "page-header visible-xs">
	<h4>Бекапы</h4>
</header>

<div class = "table-responsive">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'backup-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {pager}',
			'selectableRows' => 0,
			'columns' => array(
				array(
					'name' => 'Время создания',
					'type' => 'raw',
					'value' => '$data->formatted_timestamp'
				),
				array('name' => 'Размер', 'value' => '$data->size'),
				array(
					'name' => 'Длительность создания*, с',
					'type' => 'raw',
					'value' => '$data->create_duration',
					'htmlOptions' => array('class' => 'backup_create_duration')
				),
				array(
					'class' => 'CButtonColumn',
					'header' => 'Скачать',
					'template' => '{download}',
					'buttons' => array(
						'download' => array(
							'label' =>
								'<span class = "glyphicon glyphicon-'
								. 'download-alt"></span>',
							'url' => '$data->link',
							'imageUrl' => false,
							'options' => array('title' => 'Скачать')
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'rowCssClassExpression' =>
				'Backup::getRowClassByCreateDuration($data->create_duration)',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет бекапов.',
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

<p class = "small-text">
	* Максимальная длительность исполнения скрипта на сервере &mdash;
	<strong><?php
		$maximal_execution_time = ini_get('max_execution_time');
		echo $maximal_execution_time !== false
			? $maximal_execution_time
			: '(не доступно)';
	?> с</strong>. Жёлтым отмечены бекапы, время создания которых составило
	более <strong><?=
		round(Constants::BACKUPS_CREATE_SOFT_LIMIT * 100)
	?>%</strong> от максимального. Красным отмечены бекапы, время создания
	которых составило более <strong><?=
		round(Constants::BACKUPS_CREATE_HARD_LIMIT * 100)
	?>%</strong> от максимального.
</p>
