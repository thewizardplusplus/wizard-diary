<?php

class DayStateColumn extends CDataColumn {
	public function __construct(CGridView $grid) {
		parent::__construct($grid);

		$this->value = '';
		$this->htmlOptions = array('class' => 'day-completed-flag-column');
	}

	protected function renderDataCellContent($row, $data) {
		$this->grid->controller->widget(
			'application.widgets.DayStateWidget',
			array(
				'stats' => $data
			)
		);
	}
}
