<?php

class PointColumn extends CDataColumn {
	public function __construct(CGridView $grid) {
		parent::__construct($grid);
		$this->value = '$data->text';
	}

	protected function renderDataCellContent($row, $data) {
		$result = $data->text;
		if (empty($result)) {
			return;
		}

		$result = CHtml::dropDownList('point' . $data->id . '_state_list',
			$data->state, array(
				'INITIAL' => 'Активный',
				'SATISFIED' => 'Выполнен',
				'NOT_SATISFIED' => 'Не выполнен',
				'CANCELED' => 'Отменён'
			), array(
				'onchange' =>
					'jQuery("#point_list").yiiGridView("update", {' .
						'type: "POST",' .
						'url: "?r=point/update&id=' . $data->id . '",' .
						'data: { "Point[state]": jQuery(this).attr("value") ' .
							'},' .
						'success: function(data) {' .
							'jQuery("#point_list").yiiGridView("update");' .
						'}' .
					'});'
			)) . ' ' . $result;

		echo $result;
	}
}
