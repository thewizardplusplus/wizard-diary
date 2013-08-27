<?php

class PointColumn extends CDataColumn {
	public function __construct(CGridView $grid) {
		parent::__construct($grid);
		$this->value = '$data->text';
	}

	protected function renderDataCellContent($row, $data) {
		$result = $data->text;
		if ($data->state == 'SATISFIED') {
			$result = sprintf('<span style = "text-decoration: line-through;">'
				. '%s</span>', $result);
		}

		$prefix = '<input type = "checkbox" disabled = "disabled"%s /> ';
		if ($data->state == 'NOT_SATISFIED') {
			$prefix = '<strong>[X]</strong> ';
		}
		$result = $prefix . $result;

		$result = sprintf($result, $data->state == 'SATISFIED' ? ' checked = ' .
			'"checked"' : '');

		if ($data->state == 'CANCELED') {
			$result = sprintf('<span style = "text-decoration: line-through;">'
			. '%s</span>', $result);
		}

		echo $result;
	}
}
