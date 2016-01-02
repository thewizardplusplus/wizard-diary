<?php
	/**
	 * @var AccessController $this
	 * @var CActiveDataProvider $data_provider
	 */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/countries_codes.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/access_data_loader.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/whitelist_cleaning_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/whitelist_cleaning.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Белый список';
?>

<header class = "page-header clearfix">
	<div class = "pull-right whitelist-controls-container">
		<?= CHtml::beginForm(
			$this->createUrl('access/whitelist'),
			'post',
			array('class' => 'form-inline whitelist-clean-form')
		) ?>
			<?= CHtml::htmlButton(
				'<span class = "glyphicon glyphicon-remove"></span> Очистить',
				array(
					'class' => 'btn btn-danger whitelist-clean-button',
					'type' => 'submit'
				)
			) ?>
		<?= CHtml::endForm() ?>
	</div>

	<h4>Белый список</h4>
</header>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'whitelist',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'header' => 'IP*',
					'name' => 'ip',
					'htmlOptions' => array('class' => 'access-ip')
				),
				array(
					'header' => 'User-Agent',
					'name' => 'user_agent',
					'htmlOptions' => array('class' => 'access-user-agent')
				),
				array(
					'header' => 'Время последней авторизации',
					'value' =>
						'"<time>"'
							. '. $data->getFormattedTimestamp()'
						. '. "</time>"',
					'type' => 'raw'
				),
				array(
					'header' => 'Количество авторизаций',
					'name' => 'number'
				)
			),
			'rowCssClassExpression' =>
				'"access-data"'
				. '. ($data->isActual() ? " success" : "")',
			'rowHtmlOptionsExpression' => 'array('
				. '"data-ip" => CHtml::encode($data->ip),'
				. '"data-decode-ip-url" =>'
					. '$this->controller->createUrl('
						. '"access/decodeIp",'
						. 'array('
							. '"ip" => rawurlencode($data->ip)'
						. ')'
					. '),'
				. '"data-user-agent" => CHtml::encode($data->user_agent),'
				. '"data-decode-user-agent-url" =>'
					. '$this->controller->createUrl('
						. '"access/decodeUserAgent",'
						. 'array('
							. '"user_agent" => rawurlencode($data->user_agent)'
						. ')'
					. ')'
			. ')',
			'itemsCssClass' => 'table table-striped',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'afterAjaxUpdate' => 'function() { AccessData.load(); }',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет записей.',
			'summaryText' => 'Записи {start}-{end} из {count}.',
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

<p class = "small-text access-log-legend">
	* Зелёным отмечены авторизации, которые произошли не позднее суток назад
	(т. е. для которых куки автологина ещё живы).
</p>

<div class = "modal whitelist-cleaning-dialog">
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
					Ты точно хочешь очистить белый список?
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
