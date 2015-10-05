<?php
	/**
	 * @var DailyPointController $this
	 * @var DailyPoint $model
	 * @var CActiveDataProvider $data_provider
	 */

	Yii::app()->getClientScript()->registerPackage('sortable');
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/jquery.jeditable.min.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/purl.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/deleting_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/ajax_error_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/daily_point_list.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/daily_points_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/daily_points_adding.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Ежедневно';
?>

<div class = "clearfix header-with-button">
	<button
		class = "btn btn-primary pull-right add-daily-points-button"
		data-add-daily-points-url = "<?=
			$this->createUrl('point/addDailyPoints')
		?>">
		<span class = "glyphicon glyphicon-share-alt"></span> Добавить
	</button>
</div>

<div class = "table-responsive">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'daily-point-list',
			'dataProvider' => $data_provider,
			'template' => '{items}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' =>
						'"<span '
							. 'id = \"daily-point-text-" . $data->id . "\" '
							. 'class = \"daily-point-text\" '
							. 'data-text = '
								. '\"" . PointFormatter::encodePointText('
									. '$data->text'
								. ') . "\" '
							. 'data-update-url = '
								. '\"" . $this->grid->controller->createUrl('
									. '"dailyPoint/update",'
									. 'array("id" => $data->id)'
								. ') . "\" '
							. 'data-saving-icon-url = '
								. '\"" . Yii::app()->request->baseUrl'
								. '. "/images/processing-icon.gif\">"'
						. '. PointFormatter::formatPointText($data->text) .'
						. '"</span>"'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{up} {down}',
					'buttons' => array(
						'down' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-arrow-down">'
									. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"dailyPoint/update",'
									. 'array('
										. '"id" => $data->id,'
										. '"order" => $data->order + 3'
									. ')'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Опустить'),
							'click' =>
								'function() {'
									. 'return DailyPointList.move('
										. '$(this).attr("href")'
									. ');'
								. '}'
						),
						'up' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-arrow-up">'
									. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"dailyPoint/update",'
									. 'array('
										. '"id" => $data->id,'
										. '"order" => $data->order - 3'
									. ')'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Поднять'),
							'click' =>
								'function() {'
									. 'return DailyPointList.move('
										. '$(this).attr("href")'
									. ');'
								. '}'
						)
					)
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{update} {delete}',
					'buttons' => array(
						'update' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-pencil">'
									. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"dailyPoint/update",'
									. 'array('
										. '"id" => $data->id,'
										. '"_id" => $data->id'
									. 	')'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Изменить пункт'),
							'click' =>
								'function() {'
									. 'return DailyPointList.editing(this);'
								. '}'
						),
						'delete' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-trash">'
									. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"dailyPoint/delete",'
									. 'array('
										. '"id" => $data->id,'
										. '"_id" => $data->id'
									. ')'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Удалить пункт'),
							'click' =>
								'function() {'
									. 'return DailyPointList.deleting(this);'
								. '}'
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'loadingCssClass' => 'wait',
			'afterAjaxUpdate' => 'function() { DailyPointList.initialize(); }',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет ежедневных пунктов.'
		)
	); ?>
</div>

<?= CHtml::beginForm('#', 'post', array('id' => 'daily-point-addition-form')) ?>
	<div class = "input-group">
		<?= CHtml::textField(
			'DailyPoint_text',
			'',
			array('class' => 'form-control')
		) ?>
			<a
				class = "input-group-addon add-daily-point-button"
				href = "<?= $this->createUrl('dailyPoint/create') ?>">
				<span class = "glyphicon glyphicon-plus"></span>
			</a>
	</div>
<?= CHtml::endForm() ?>

<div class = "modal daily-points-dialog">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button
					class = "close"
					type = "button"
					data-dismiss = "modal"
					aria-hidden = "true">
					&times;
				</button>
				<h4 class = "modal-title">
					<span class = "glyphicon glyphicon-warning-sign"></span>
					Внимание!
				</h4>
			</div>

			<div class = "modal-body">
				<p>
					Ты точно хочешь добавить ежедневные пункты?
				</p>
			</div>

			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary ok-button">
					OK
				</button>
				<button
					class = "btn btn-default"
					type = "button"
					data-dismiss = "modal">
					Отмена
				</button>
			</div>
		</div>
	</div>
</div>

<div class = "modal deleting-dialog">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button
					class = "close"
					type = "button"
					data-dismiss = "modal"
					aria-hidden = "true">
					&times;
				</button>
				<h4 class = "modal-title">
					<span class = "glyphicon glyphicon-warning-sign"></span>
					Внимание!
				</h4>
			</div>

			<div class = "modal-body">
				<p>
					Ты точно хочешь удалить
					<span class = "point-description"></span>?
				</p>
			</div>

			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary ok-button">
					OK
				</button>
				<button
					class = "btn btn-default"
					type = "button"
					data-dismiss = "modal">
					Отмена
				</button>
			</div>
		</div>
	</div>
</div>
