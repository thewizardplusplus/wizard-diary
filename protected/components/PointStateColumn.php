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
			));

		echo $result;
	}
}
