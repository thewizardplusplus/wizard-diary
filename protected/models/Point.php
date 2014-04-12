<?php

class Point extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function renumberOrderFieldsForDate($date) {
		Yii::app()->db->createCommand('SET @order = 1')->execute();
		Point::model()->updateAll(
			array('order' => new CDbExpression('(@order := @order + 2)')),
			array(
				'condition' => 'date = "' . $date . '"',
				'order' => '`order`'
			)
		);
	}

	public function tableName() {
		return '{{points}}';
	}

	public function rules() {
		return array(
			array('text', 'safe'),
			array(
				'state',
				'in',
				'range' => array(
					'INITIAL',
					'SATISFIED',
					'NOT_SATISFIED',
					'CANCELED'
				)
			),
			array('check', 'boolean', 'falseValue' => 0, 'trueValue' => 1),
			array('order', 'numerical')
		);
	}

	public function getStateClass() {
		return strtolower(str_replace('_', '-', $this->state));
	}

	public function getRowClassByState() {
		return self::$row_classes_for_states[$this->state];
	}

	public function getMyDate() {
		$start_date =
			Yii::app()
			->db
			->createCommand('SELECT MIN(date) FROM ' . $this->tableName())
			->queryScalar();

		$difference = date_diff(
			date_create($start_date),
			date_create($this->date)
		);
		$days = $difference->days;

		$my_day = $days % Constants::DAYS_IN_MY_YEAR + 1;
		if ($my_day < 10) {
			$my_day = '0' . $my_day;
		}
		$my_year = round($days / Constants::DAYS_IN_MY_YEAR) + 1;
		if ($my_year < 10) {
			$my_year = '0' . $my_year;
		}

		return $my_day . '.' . $my_year;
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			if ($this->isNewRecord) {
				$this->date = date('Y-m-d');
			} elseif (empty($this->text)) {
				$this->state = 'INITIAL';
				$this->check = 0;
			}
		}

		return $result;
	}

	private static $row_classes_for_states = array(
		'INITIAL' => '',
		'SATISFIED' => 'success',
		'NOT_SATISFIED' => 'danger',
		'CANCELED' => 'success'
	);
}
