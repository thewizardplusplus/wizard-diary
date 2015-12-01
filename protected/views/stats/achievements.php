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

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var DEFAULT_CURRENT_URL = \''
				. CJavaScript::quote($this->createUrl('stats/achievements'))
			. '\';',
		CClientScript::POS_HEAD
	);
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

<div class = "clearfix achievements-selects-container">
	<?= CHtml::beginForm(
		'#',
		'get',
		array('class' => 'form-inline pull-right achievements-selects-form')
	) ?>
		<div class = "form-group">
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
		</div>

		<div class = "form-group">
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
		</div>

		<?= CHtml::htmlButton(
			'<span class = "glyphicon glyphicon-remove"></span> '
				. '<span class = "visible-xs-inline">Очистить</span>',
			array(
				'class' =>
					'btn btn-default blue-button achievement-list-reset-button',
				'type' => 'submit'
			)
		) ?>
	<?= CHtml::endForm() ?>

	<p class = "pull-left">
		Получено <strong><span class = "achievement-counter-view"><?=
			$this->formatAchievements(
				$achievements_provider->getTotalItemCount()
			)
		?></span></strong>.
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
					. 'AchievementsSelects.afterUpdate();'
					. 'AchievementsIcons.afterUpdate();'
				. '}',
			'ajaxUpdateError' =>
				'function(xhr, text_status) {'
					. 'AjaxErrorDialog.handler(xhr, text_status);'
				. '}',
			'emptyText' => 'Нет достижений.',
			'summaryText' =>
				'Достижения {start}-{end} из '
				. '<span class = "achievement-list-total-counter">'
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
