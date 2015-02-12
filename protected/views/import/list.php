<?php
	/* @var $this ImportController */
	/* @var $data_provider CActiveDataProvider */

	$this->pageTitle = Yii::app()->name . ' - Импорт';
?>

<header class = "page-header visible-xs">
	<h4>Импорт</h4>
</header>

<div>
	<div class = "clearfix">
		<a
			class = "btn btn-primary pull-right"
			href = "<?= $this->createUrl('import/create') ?>">
			<span class = "glyphicon glyphicon-plus"></span>
			Создать импорт
		</a>
	</div>
	<hr />
</div>

<?php
	$this->widget(
		'zii.widgets.CListView',
		array(
			'id' => 'import-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'enableHistory' => true,
			'itemView' => '_view',
			'separator' => '<hr />',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет импорта.',
			'summaryText' => 'Импорт {start}-{end} из {count}.',
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
	);
?>
