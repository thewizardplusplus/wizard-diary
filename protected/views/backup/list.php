<?php
	/**
	 * @var BackupController $this
	 * @var CArrayDataProvider $data_provider
	 * @var string $last_backup_date
	 * @var bool $has_current_difference
	 */

	$this->pageTitle = Yii::app()->name . ' - Бекапы';
?>

<header class = "page-header clearfix header-with-button">
	<a
		class = "btn btn-default pull-right current-difference-button"
		href = "<?= $this->createUrl(
			'backup/currentDiff',
			array('file' => $last_backup_date)
		) ?>"
		<?= is_null($last_backup_date) || !$has_current_difference
			? 'disabled = "disabled"'
			: '' ?>>
		<span class = "glyphicon glyphicon-random"></span> Текущие изменения
	</a>

	<h4>Бекапы</h4>
</header>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'backup-list',
			'dataProvider' => $data_provider,
			'template' => '<div class="item-wrapper">{items}</div> {summary} {pager}',
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
					'header' => 'Длительность сохранения*, с',
					'name' => 'save_duration',
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
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-download-alt">'
								. '</span>',
							'url' => '$data->link',
							'imageUrl' => false,
							'options' => array('title' => 'Скачать')
						)
					)
				),
				array(
					'class' => 'CButtonColumn',
					'header' => 'Изменения',
					'template' => '{diff}',
					'buttons' => array(
						'diff' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-random">'
								. '</span>',
							'url' => '$this->grid->controller->createUrl('
									. '"backup/diff",'
									. 'array('
										. '"file" => $data->filename,'
										. '"previous_file" =>'
											. '$data->previous_filename'
									. ')'
								.')',
							'imageUrl' => false,
							'options' => array('title' => 'Изменения'),
							'visible' => '$data->has_difference'
						)
					)
				)
			),
			'itemsCssClass' => 'table table-striped',
			'loadingCssClass' => 'wait',
			'rowCssClassExpression' =>
				'Backup::getRowClassByCreateDuration('
					. '$data->create_duration,'
					. '$data->save_duration'
				. ')',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'BackupUtils.error(xhr, text_status);'
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
	?> с</strong>. Жёлтым отмечены бекапы, время создания или сохранения которых
	составило более <strong><?=
		round(Constants::BACKUPS_CREATE_SOFT_LIMIT * 100)
	?>%</strong> от максимального. Красным отмечены бекапы, время создания или
	сохранения которых составило более <strong><?=
		round(Constants::BACKUPS_CREATE_HARD_LIMIT * 100)
	?>%</strong> от максимального.
</p>
