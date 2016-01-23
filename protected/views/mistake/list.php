<?php
	/**
	 * @var MistakeController $this
	 * @var CArrayDataProvider $data_provider
	 * @var array $daily_stats
	 */

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var SPELLING_ADDING_URL = \''
				. $this->createUrl('spelling/add')
			. '\';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/mistakes_adding_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/mistake_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Ошибки';
?>

<header class = "page-header">
	<h4>Ошибки</h4>
</header>

<div class = "clearfix mistakes-controls-container">
	<p class = "pull-left">
		Ошибки найдены в <strong><span class = "mistakes-counter-view"><?=
			$this->formatMistakes($data_provider->getTotalItemCount())
		?></span></strong>.
	</p>
</div>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'ExtendedGridView',
		array(
			'id' => 'mistake-list',
			'dataProvider' => $data_provider,
			'dailyStats' => $daily_stats,
			'template' => '{items} {summary} {pager}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' =>
						'"<a '
							. 'href = \""'
								. '. $this->grid->controller->createUrl('
									. '"day/view",'
									. 'array("date" => $data["date"])'
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
						. '</a>"',
					'htmlOptions' => array('class' => 'date-column')
				),
				array('type' => 'raw', 'value' => '$data["text"]'),
				array(
					'class' => 'CButtonColumn',
					'template' => '{correct}',
					'buttons' => array(
						'correct' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-pencil">'
									. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"day/update",'
									. 'array('
										. '"date" => $data["date"],'
										. '"line" =>'
											. '$this'
											. '->grid'
											. '->controller'
											. '->calculateLine('
												. '$data,'
												. '$this->grid->dailyStats'
											. ')'
									. ')'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Исправить ошибку'),
							'visible' => '!$data["daily"]'
						)
					)
				)
			),
			'itemsCssClass' => 'table table-striped',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'afterAjaxUpdate' =>
				'function() {'
					. 'MistakeList.afterUpdate();'
				. '}',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет ошибок.',
			'summaryText' =>
				'Ошибки {start}-{end} из '
				. '<span class = "mistake-list-total-counter">'
					. '{count}'
				. '</span>.',
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

<div class = "modal custom-spellings-adding-dialog" tabindex = "-1">
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
					Ты точно хочешь добавить слово
					<strong>&laquo;<span class = "wrong-word">
					</span>&raquo;</strong>
					в словарь?
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
