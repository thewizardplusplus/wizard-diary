<?php
	/**
	 * @var DailyPointController $this
	 * @var CActiveDataProvider $data_provider
	 * @var DailyPointForm $model
	 * @var string $day_container_class
	 * @var string $year_container_class
	 * @var string $date
	 * @var object $my_date
	 */

	Yii::app()->getClientScript()->registerPackage('purl');
	Yii::app()->getClientScript()->registerPackage('jeditable');
	Yii::app()->getClientScript()->registerPackage('jquery.ui');
	Yii::app()->getClientScript()->registerPackage('sortable');

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var DAILY_POINT_ORDER_URL = \''
			. $this->createUrl('dailyPoint/order')
		. '\';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var DAYS_IN_MY_YEAR = ' . Constants::DAYS_IN_MY_YEAR . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var MY_DATE = ' . json_encode($my_date) . ';',
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
		CHtml::asset('scripts/daily_point_form.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Ежедневно';
?>

<header class = "page-header visible-xs-block">
	<h4>Ежедневно</h4>
</header>

<div class = "clearfix">
	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id' => 'daily-point-form',
			'enableAjaxValidation' => true,
			'enableClientValidation' => true,
			'errorMessageCssClass' => 'alert alert-danger',
			'clientOptions' => array(
				'errorCssClass' => 'has-error',
				'successCssClass' => 'has-success'
			),
			'htmlOptions' => array(
				'class' =>
					'form-inline '
					. 'panel '
					. 'panel-default '
					. 'pull-right '
					. 'daily-point-form'
			)
		)
	); ?>
		<?= $form->errorSummary(
			$model,
			NULL,
			NULL,
			array('class' => 'alert alert-danger')
		) ?>

		<div class = "form-group<?= $day_container_class ?>">
			<?= $form->labelEx(
				$model,
				'day',
				array('class' => 'control-label')
			) ?>
			<?= $form->textField(
				$model,
				'day',
				array(
					'class' => 'form-control',
					'autocomplete' => 'off',
					'min' => 1,
					'max' => $my_date->day,
					'required' => 'required'
				)
			) ?>
			<?= $form->error(
				$model,
				'day',
				array('hideErrorMessage' => true)
			) ?>
		</div>

		<div class = "form-group<?= $year_container_class ?>">
			<?= $form->labelEx(
				$model,
				'year',
				array('class' => 'control-label')
			) ?>
			<?= $form->textField(
				$model,
				'year',
				array(
					'class' => 'form-control',
					'autocomplete' => 'off',
					'min' => 1,
					'max' => $my_date->year,
					'required' => 'required'
				)
			) ?>
			<?= $form->error(
				$model,
				'year',
				array('hideErrorMessage' => true)
			) ?>
		</div>

		<?= CHtml::htmlButton(
			'<span class = "glyphicon glyphicon-share-alt"></span> Добавить',
			array(
				'class' => 'btn btn-primary add-daily-points-button',
				'type' => 'submit'
			)
		) ?>
	<?php $this->endWidget(); ?>
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
							. 'data-id = \"" . $data->id . "\" '
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
			'itemsCssClass' => 'table table-striped',
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
