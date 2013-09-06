<?php

class PointStateColumn extends CDataColumn {
	public function __construct(CGridView $grid) {
		parent::__construct($grid);
		$this->value = '';
	}

	protected function renderDataCellContent($row, $data) {
		if (empty($data->text)) {
			return;
		}

		$id = $data->id;
		if ($data->state == 'INITIAL') {
			$main_link_classes = 'glyphicon glyphicon-exclamation-sign ' .
				'state-initial';
		} else if ($data->state == 'SATISFIED') {
			$main_link_classes = 'glyphicon glyphicon-ok-sign state-satisfied';
		} else if ($data->state == 'NOT_SATISFIED') {
			$main_link_classes = 'glyphicon glyphicon-remove-sign ' .
				'state-not-satisfied';
		} else if ($data->state == 'CANCELED') {
			$main_link_classes = 'glyphicon glyphicon-minus-sign ' .
				'state-canceled';
		}
		echo <<<DROPDOWN_LIST
<div class = "dropdown">
	<a id = "point$id-state-label" data-toggle = "dropdown" href = "#" role =
		"button">
		<span class = "$main_link_classes" style = "font-size: larger;"></span>
	</a>
	<ul id = "point$id-state-list" class = "dropdown-menu" role = "menu"
		aria-labelledby = "point$id-state-label">
		<li role = "presentation">
			<a class = "state-initial" role = "menuitem" tabindex = "-1" href =
				"#" onclick = "return processPointStateChoise($id, 'INITIAL');">
				<span class = "glyphicon glyphicon-exclamation-sign" style =
					"font-size: larger;"></span> Активный
			</a>
		</li>
		<li role = "presentation">
			<a class = "state-satisfied" role = "menuitem" tabindex = "-1" href
				= "#" onclick =
				"return processPointStateChoise($id, 'SATISFIED');">
				<span class = "glyphicon glyphicon-ok-sign" style =
					"font-size: larger;"></span> Выполнен
			</a>
		</li>
		<li role = "presentation">
			<a class = "state-not-satisfied" role = "menuitem" tabindex = "-1"
				href = "#" onclick =
				"return processPointStateChoise($id, 'NOT_SATISFIED');">
				<span class = "glyphicon glyphicon-remove-sign" style =
					"font-size: larger;"></span> Не выполнен
			</a>
		</li>
		<li role = "presentation">
			<a class = "state-canceled" role = "menuitem" tabindex = "-1" href =
				"#" onclick =
				"return processPointStateChoise($id, 'CANCELED');">
				<span class = "glyphicon glyphicon-minus-sign" style =
					"font-size: larger;"></span> Отменён
			</a>
		</li>
	</ul>
</div>
DROPDOWN_LIST;
	}
}
