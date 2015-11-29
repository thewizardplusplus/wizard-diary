<?php
	/**
	 * @var StatsController $this
	 * @var CArrayDataProvider $achievements_provider
	 * @var array $achievements_levels
	 * @var array $achievements_texts
	 */

	Yii::app()->getClientScript()->registerPackage('jdenticon');
	Yii::app()->getClientScript()->registerPackage('bootstrap-select');
	Yii::app()->getClientScript()->registerPackage('bootstrap-select-i18n');
	Yii::app()->getClientScript()->registerPackage('mobile-detect');

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/achievements_selects.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/achievements_icons.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Статистика: достижения';
?>

<header class = "page-header">
	<h4>Статистика: достижения</h4>
</header>

<div class = "clearfix">
	<div class = "pull-right">
		<?= CHtml::dropDownList(
			'achievements_levels_select',
			'',
			$achievements_levels,
			array(
				'class' => 'achievement-select',
				'multiple' => 'multiple',
				'title' => 'Достижения',
				'data-selected-text-format' => 'count > 0'
			)
		) ?>
		<?= CHtml::dropDownList(
			'achievements_texts_select',
			'',
			$achievements_texts,
			array(
				'class' => 'achievement-select',
				'multiple' => 'multiple',
				'title' => 'Пункты',
				'data-selected-text-format' => 'count > 0',
				'data-size' => count($achievements_levels)
			)
		) ?>
		<?= CHtml::htmlButton(
			'<span class = "glyphicon glyphicon-remove"></span>',
			array('class' => 'btn btn-default achievement-list-reset-button')
		) ?>
	</div>

	<p class = "pull-left achievement-counter">
		Получено <strong><?= $this->formatAchievements(
			$achievements_provider->getTotalItemCount()
		) ?></strong>.
	</p>
</div>

<div class = "clearfix">
	<?php $this->widget(
		'zii.widgets.CListView',
		array(
			'id' => 'achievements-list',
			'dataProvider' => $achievements_provider,
			'itemView' => '_achievements_view',
			'template' => '{items} {summary} {pager}',
			'enableHistory' => true,
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'afterAjaxUpdate' =>
				'function() {'
					. 'AchievementsIcons.afterUpdate();'
				. '}',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет достижений.',
			'summaryText' => 'Достижения {start}-{end} из {count}.',
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
