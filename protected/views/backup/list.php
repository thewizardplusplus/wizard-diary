<?php
	/**
	 * @var BackupController $this
	 * @var CArrayDataProvider $data_provider
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/backuping.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Бекапы';
?>

<header class = "page-header visible-xs">
	<h4>Бекапы</h4>
</header>

<div>
	<div class = "clearfix">
		<button
			class = "btn btn-primary pull-right create-backup-button"
			data-create-backup-url = "<?= $this->createUrl('backup/create') ?>"
			data-dropbox-app-key = "<?= Constants::DROPBOX_APP_KEY ?>"
			data-dropbox-redirect-url = "<?=
				Constants::DROPBOX_REDIRECT_URL
			?>">
			<img
				src = "<?=
					Yii::app()->request->baseUrl
				?>/images/processing-icon.gif"
				alt = "..." />
			<span class = "glyphicon glyphicon-compressed"></span>
			<span>Создать бекап</span>
		</button>
	</div>
	<hr />
</div>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'backup-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'header' => 'Время создания',
					'name' => 'formatted_timestamp',
					'type' => 'raw'
				),
				array('header' => 'Размер', 'name' => 'size'),
				array(
					'header' => 'Длительность создания*, с',
					'name' => 'create_duration',
					'type' => 'raw',
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
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет бекапов.',
			'summaryText' => 'Бекапы {start}-{end} из {count}.',
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

<p class = "small-text backups-legend">
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
