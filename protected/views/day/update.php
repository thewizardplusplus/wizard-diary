<?php
	/**
	 * @var DayController $this
	 * @var string $points_description
	 * @var string $my_date
	 * @var string $date
	 * @var string $raw_date
	 * @var array $stats
	 * @var array $point_hierarchy
	 * @var int $line
	 */

	Yii::app()->getClientScript()->registerPackage('ace');
	Yii::app()->getClientScript()->registerPackage('ace-language-tools');
	// need only for 'ace/mode/behaviour/cstyle' module
	Yii::app()->getClientScript()->registerPackage('ace-mode-javascript');
	Yii::app()->getClientScript()->registerPackage('mobile-detect');

	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var POINT_HIERARCHY = ' . json_encode($point_hierarchy) . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var LINE = ' . $line . ';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var CHECKING_URL = \'' . $this->createUrl('mistake/check') . '\';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		base64_encode(uniqid(rand(), true)),
		'var SPELLING_ADDING_URL = \''
				. $this->createUrl('spelling/add')
			. '\';',
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/point_unit.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/day_close_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/ace_wizard_diary_highlight_rules.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/ace_mode_wizard_diary.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/mistakes_adding_dialog.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/day_editor.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - ' . $my_date;
?>

<header class = "page-header clearfix header-with-button">
	<button
		class = "btn btn-default pull-right close-button"
		title = "Закрыть"
		data-date = "<?= $date ?>"
		data-my-date = "<?= $my_date ?>"
		data-view-url = "<?=
			$this->createUrl('day/view', array('date' => $raw_date))
		?>">
		<span class = "glyphicon glyphicon-remove"></span>
	</button>
	<button
		class = "btn btn-primary pull-right save-day-button"
		title = "Сохранить"
		data-save-url = "<?=
			$this->createUrl('day/update', array('date' => $raw_date))
		?>">
		<img
			src = "<?=
				Yii::app()->request->baseUrl
			?>/images/processing-icon.gif"
			alt = "..." />
		<span class = "glyphicon glyphicon-floppy-disk"></span>
	</button>

	<h4 class = "clearfix day-editor-header">
		<time title = "<?= $date ?>"><?= $my_date ?></time>

		<span
			class = "label label-<?=
				$stats['completed']
					? 'success'
					: 'primary'
			?> day-completed-flag"
			title = "<?= $stats['completed'] ? 'Завершён' : 'Не завершён' ?>">
			<span
				class = "glyphicon glyphicon-<?=
					$stats['completed']
						? 'check'
						: 'unchecked'
				?>">
			</span>
		</span>
		<span
			class = "label label-success saved-flag"
			title = "Сохранено">
			<span class = "glyphicon glyphicon-floppy-saved"></span>
		</span>
		<span
			class = "label label-success spellcheck-flag"
			title = "Нет ошибок">
			<img
				src = "<?=
					Yii::app()->request->baseUrl
				?>/images/processing-icon.gif"
				alt = "..." />
			<span class = "glyphicon glyphicon-education"></span>
		</span>
	</h4>

	<p class = "pull-left unimportant-text italic-text">
		<span class = "day-satisfied-view" data-date = "<?= $date ?>">
			<?= $this->formatSatisfiedCounter($stats['satisfied']) ?>
		</span>
		<span class = "number-of-daily-points-view"><?=
			$stats['daily']
		?></span>+<span class = "number-of-points-view"><?=
			PointFormatter::formatNumberOfPoints($stats['projects'])
		?></span>
	</p>
</header>

<?= CHtml::beginForm(
	$this->createUrl('day/update', array('date' => $raw_date)),
	'post',
	array('class' => 'day-form')
) ?>
	<div class = "form-group">
		<?= CHtml::label('Описание пунктов', 'points_description') ?>

		<div>
			<ul class = "nav nav-tabs">
				<li class = "active">
					<a href = "#default" data-toggle = "tab">По умолчанию</a>
				</li>
				<li>
					<a href = "#mobile" data-toggle = "tab">Мобильный</a>
				</li>
			</ul>

			<div class = "tab-content">
				<div id = "default" class = "tab-pane active">
					<div id = "day-editor"><?=
						CHtml::encode($points_description)
					?></div>
				</div>
				<div id = "mobile" class = "tab-pane">
					<?= CHtml::textArea(
						'day-mobile-editor',
						$points_description,
						array('class' => 'form-control')
					) ?>
				</div>
			</div>
		</div>
	</div>
<?= CHtml::endForm() ?>

<div class = "modal day-close-dialog" tabindex = "-1">
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
					День <time class = "day-date"></time> не сохранён.
					Закрытие редактора может привести к потере последних
					изменений. Так что ты хочешь сделать?
				</p>
			</div>

			<div class = "modal-footer">
				<button type = "button" class = "btn btn-primary save-button">
					<span class = "glyphicon glyphicon-floppy-disk"></span>
					Сохранить и закрыть
				</button>
				<button type = "button" class = "btn btn-danger close-button">
					<span class = "glyphicon glyphicon-remove"></span>
					Закрыть
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
