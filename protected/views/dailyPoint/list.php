<?php
	/**
	 * @var DailyPointController $this
	 * @var CActiveDataProvider $data_provider
	 * @var DailyPointForm $model
	 * @var string $day_container_class
	 * @var string $year_container_class
	 * @var string $date
	 * @var object $my_date
	 * @var int $number_of_daily_points
	 */

	Yii::app()->getClientScript()->registerPackage('jquery.ui');

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
		CHtml::asset('scripts/daily_points_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/daily_point_form.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Ежедневно';
?>

<header class = "page-header clearfix header-with-button">
	<a
		class = "btn btn-default pull-right"
		href = "<?= $this->createUrl('dailyPoint/update') ?>">
		<span class = "glyphicon glyphicon-pencil"></span> Изменить
	</a>

	<h4 class = "clearfix">Ежедневно</h4>

	<p class = "pull-left unimportant-text italic-text">
		<?= PointFormatter::formatNumberOfPoints($number_of_daily_points) ?>
	</p>
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
			'dataProvider' => $data_provider,
			'template' => '{items}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' => 'PointFormatter::formatPointText($data->text)'
				)
			),
			'itemsCssClass' => 'table table-striped',
			'emptyText' => 'Нет ежедневных пунктов.'
		)
	); ?>
</div>

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
