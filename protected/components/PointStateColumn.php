<?php

class PointStateColumn extends CDataColumn {
	public function __construct(CGridView $grid) {
		parent::__construct($grid);

		$this->value = '';
		$this->htmlOptions = array(
			'class' => 'button-column narrow point-state-column'
		);
	}

	protected function renderDataCellContent($row, $data) {
		if (empty($data->text)) {
			return;
		}

		$state_class = $data->getStateClass();
		$state_icon_name = self::$state_icons_names[$data->state];
		$update_url = $this->grid->controller->createUrl(
			'point/update',
			array('id' => $data->id)
		);

		$this->grid->controller->renderPartial(
			'_point_state_column',
			array(
				'state_class' => $state_class,
				'state_icon_name' => $state_icon_name,
				'point_id' => $data->id,
				'update_url' => $update_url
			)
		);
	}

	private static $state_icons_names = array(
		'INITIAL' => 'exclamation-sign',
		'SATISFIED'=> 'ok-sign',
		'NOT_SATISFIED' => 'remove-sign',
		'CANCELED' => 'minus-sign'
	);
}
