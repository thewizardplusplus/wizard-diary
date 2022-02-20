<?php

class Point extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function getRenumberOrderSql($date) {
		return sprintf(
			"SET @order = 1;\n"
				. "UPDATE {{points}}\n"
				. "SET `order` = (@order := @order + 2)\n"
				. "WHERE `date` = %s\n"
				. "ORDER BY `daily` DESC, `order`, `id`;",
			Yii::app()->db->quoteValue($date)
		);
	}

	public function tableName() {
		return '{{points}}';
	}

	public function rules() {
		return array(
			array(
				'state',
				'in',
				'range' => array(
					'INITIAL',
					'SATISFIED',
					'NOT_SATISFIED',
					'CANCELED'
				)
			)
		);
	}

	public function getStateClass() {
		return strtolower(str_replace('_', '-', $this->state));
	}

	public function getRowClassByState() {
		return 'point-row point-' . $this->id . ' '
			. (!$this->daily
				? self::$row_classes_for_states[$this->state]
				: 'warning');
	}

	private static $row_classes_for_states = array(
		'INITIAL' => '',
		'SATISFIED' => 'success',
		'NOT_SATISFIED' => 'danger',
		'CANCELED' => 'success'
	);
}
