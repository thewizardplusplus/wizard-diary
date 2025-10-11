<?php

class DayStateColumn extends CDataColumn {
	public function __construct(CGridView $grid) {
		parent::__construct($grid);

		$this->value = '';
		$this->htmlOptions = array('class' => 'day-completed-flag-column');
	}

	protected function renderDataCellContent($row, $data) {
		$is_completed = $data['completed'];
		$is_skipped = $is_completed && $data['skipped'] && $data['daily'] > 0;

		$label_class = $is_skipped
			? 'label-default'
			: ($is_completed
				? 'label-success'
				: 'label-primary');
		$label_title = $is_skipped
			? 'Пропущен'
			: ($is_completed
				? 'Завершён'
				: 'Не завершён');
		$label_glyphicon = $is_skipped
			? 'glyphicon-modal-window'
			: ($is_completed
				? 'glyphicon-check'
				: 'glyphicon-unchecked');

		$this->grid->controller->renderPartial(
			'_day_state_column',
			array(
				'label_class' => $label_class,
				'label_title' => $label_title,
				'label_glyphicon' => $label_glyphicon
			)
		);
	}
}
