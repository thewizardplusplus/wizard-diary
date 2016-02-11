<?php
	/**
	 * @var PointController $this
	 * @var DeleteByQueryForm $model
	 * @var string $query_container_class
	 * @var int $number_of_deleted_points
	 * @var CActiveDataProvider $data_provider
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/delete_by_query_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/delete_by_query.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Удаление по запросу';
?>

<header class = "page-header">
	<h4>Удаление по запросу</h4>
</header>

<?php if (!is_null($number_of_deleted_points)) { ?>
	<?php if ($number_of_deleted_points > 0) { ?>
		<p>
			<?= DeleteByQueryFormatter::formatLabelStart(
				$number_of_deleted_points
			) ?> <strong><?= PointFormatter::formatNumberOfPoints(
				$number_of_deleted_points
			) ?></strong> из <?= DeleteByQueryFormatter::formatLabelEnd(
				$data_provider->getTotalItemCount()
			) ?>:
		</p>

		<div class = "table-responsive">
			<?php $this->widget(
				'zii.widgets.grid.CGridView',
				array(
					'id' => 'point-list',
					'dataProvider' => $data_provider,
					'template' => '{items}',
					'hideHeader' => true,
					'selectableRows' => 0,
					'columns' => array(
						array(
							'type' => 'raw',
							'value' =>
								'"<a '
									. 'href = \""'
										. '. $this'
											. '->grid'
											. '->controller'
											. '->createUrl('
												. '"day/view",'
												. 'array('
													. '"date" => $data["date"]'
												. ')'
											. ') . "\">'
									. '<time '
										. 'title = \""'
											. ' . DateFormatter::formatDate('
												. '$data["date"]'
											. ') . "\">"'
										. ' . DateFormatter::formatMyDate('
											. '$data["date"]'
										. ')'
									. ' . "</time>'
								. '</a>"'
						),
						array(
							'class' => 'CButtonColumn',
							'template' => '{update}',
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
											. '"day/update",'
											. 'array("date" => $data["date"])'
										. ')',
									'imageUrl' => false,
									'options' => array(
										'title' => 'Изменить день'
									)
								)
							)
						)
					),
					'itemsCssClass' => 'table table-striped',
					'loadingCssClass' => 'wait',
					'ajaxUpdateError' =>
						'function(xhr, text_status) {'
							. 'AjaxErrorDialog.handler(xhr, text_status);'
						. '}',
					'emptyText' => 'Нет пунктов.'
				)
			); ?>
		</div>
	<?php } else { ?>
		<p>Нет пунктов для удаления.</p>
	<?php } ?>
<?php } ?>

<?php $form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'delete-by-query-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger',
		'clientOptions' => array(
			'errorCssClass' => 'has-error',
			'successCssClass' => 'has-success'
		),
		'htmlOptions' => array('class' => 'delete-by-query-form')
	)
); ?>
	<?= $form->errorSummary(
		$model,
		NULL,
		NULL,
		array('class' => 'alert alert-danger')
	) ?>

	<div class = "form-group<?= $query_container_class ?>">
		<?= $form->labelEx(
			$model,
			'query',
			array('class' => 'control-label')
		) ?>
		<div class = "input-group">
			<?= $form->textField(
				$model,
				'query',
				array(
					'class' => 'form-control query-editor',
					'autocomplete' => 'off',
					'required' => 'required'
				)
			) ?>
			<div class = "input-group-addon clean-button">
				<span class = "glyphicon glyphicon-remove"></span>
			</div>
		</div>
		<?= $form->error($model, 'query') ?>
	</div>

	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-trash"></span> Удалить',
		array(
			'class' => 'btn btn-primary delete-by-query-button',
			'type' => 'submit'
		)
	) ?>
<?php $this->endWidget(); ?>

<div class = "modal delete-by-query-dialog" tabindex = "-1">
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
					Ты точно хочешь удалить пункты, начинающиеся на
					<strong>&laquo;<span
						class = "query-view"></span>&raquo;</strong>?
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
