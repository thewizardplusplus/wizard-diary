<?php
	/**
	 * @var SpellingController $this
	 * @var CArrayDataProvider $data_provider
	 */

	Yii::app()->getClientScript()->registerPackage('URI');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/spelling_deleting_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/spellings_cleaning_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/spelling_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Словарь';
?>

<header class = "page-header clearfix header-with-button">
	<?= CHtml::htmlButton(
		'<span class = "glyphicon glyphicon-remove"></span> Очистить',
		array(
			'class' => 'btn btn-danger pull-right spellings-clean-button',
			'data-spellings-clean-url' => $this->createUrl('spelling/deleteAll')
		)
	) ?>

	<h4>Словарь</h4>
</header>

<div class = "table-responsive clearfix">
	<?php $this->widget(
		'zii.widgets.grid.CGridView',
		array(
			'id' => 'spelling-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'hideHeader' => true,
			'selectableRows' => 0,
			'enableHistory' => true,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' =>
						'"<span id = \"word-" . $data->id . "\">"'
							. '. $data->word'
						. '. "</span>"'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{delete}',
					'buttons' => array(
						'delete' => array(
							'label' =>
								'<span '
									. 'class = '
										. '"glyphicon '
										. 'glyphicon-trash">'
								. '</span>',
							'url' =>
								'$this->grid->controller->createUrl('
									. '"spelling/delete",'
									. 'array("id" => $data->id)'
								. ')',
							'imageUrl' => false,
							'options' => array('title' => 'Удалить'),
							'click' =>
								'function() {'
									. 'return SpellingList.delete(this);'
								. '}'
						)
					)
				)
			),
			'itemsCssClass' => 'table table-striped',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет слов.',
			'summaryText' => 'Слова {start}-{end} из {count}.',
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

<div class = "modal spelling-deleting-dialog">
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
					Ты точно хочешь удалить слово
					<strong>&laquo;<span class = "word-view">
					</span>&raquo;</strong>?
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

<div class = "modal spellings-cleaning-dialog">
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
					Ты точно хочешь очистить словарь?
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
