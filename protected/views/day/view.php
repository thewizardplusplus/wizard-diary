<?php
	/**
	 * @var DayController $this
	 * @var CArrayDataProvider $data_provider
	 * @var string $my_date
	 * @var string $date
	 * @var string $raw_date
	 * @var array $stats
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - ' . $my_date;
?>

<header class = "page-header clearfix header-with-button">
	<a
		class = "btn btn-default pull-right"
		href = "<?=
			$this->createUrl('day/update', array('date' => $raw_date))
		?>">
		<span class = "glyphicon glyphicon-pencil"></span> Изменить
	</a>

	<h4>
		<time title = "<?= $date ?>"><?= $my_date ?></time>

		<span
			class = "label label-<?=
				$stats['completed']
					? 'success'
					: 'primary'
			?> day-completed-flag"
			title = "<?= $stats['completed'] ? 'Завершён' : 'Не завершён' ?>"
			data-stats-url = "<?=
				$this->createUrl('day/stats', array('date' => $raw_date))
			?>">
			<span
				class = "glyphicon glyphicon-<?=
					$stats['completed']
						? 'check'
						: 'edit'
				?>">
			</span>
		</span>
	</h4>

	<p class = "unimportant-text italic-text">
		<?= PointFormatter::formatNumberOfPoints($stats['projects']) ?>
	</p>
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
							. '. PointFormatter::formatPointText($data->text)'
						. '. "</span>"'
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
