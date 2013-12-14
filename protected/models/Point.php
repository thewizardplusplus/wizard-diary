<?php

class Point extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function renumberOrderFieldsForDate($date) {
		$connection = Yii::app()->db;
		$connection->createCommand('SET @order = 0')->execute();
		Point::model()->updateAll(array('order' => new CDbExpression('(@order '
			. ':= @order + 2)')), array(
				'condition' => '`date` = ' . $connection->quoteValue($date),
				'order' => '`order`'
			));
	}

	public function tableName() {
		return '{{points}}';
	}

	public function rules() {
		return array(
			array('text', 'safe'),
			array('state', 'in', 'range' => array('INITIAL', 'SATISFIED',
				'NOT_SATISFIED', 'CANCELED')),
			array('check', 'boolean', 'trueValue' => 1, 'falseValue' => 0),
			array('order', 'numerical')
		);
	}

	public function attributeLabels() {
		return array('text' => 'Текст:');
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			if ($this->isNewRecord) {
				$this->date = date("Y-m-d");
			}

			return true;
		} else {
			return false;
		}
	}

	protected function afterSave() {
		$result = parent::beforeSave();
		if ($result) {
			self::renumberOrderFieldsForDate($this->date);
			return true;
		} else {
			return false;
		}
	}

	public function getRowClassByState() {
		if (array_key_exists($this->state, $this->row_classes_for_states)) {
			return $this->row_classes_for_states[$this->state];
		} else {
			return '';
		}
	}

	public function getMyDate() {
		$difference = date_create(Parameters::get()->start_date)->diff(
			date_create($this->date));
		$days = $difference->days;

		$my_day = $days % Constants::DAYS_IN_MY_YEAR + 1;
		if ($my_day < 10) {
			$my_day = '0' . $my_day;
		}
		$my_year = round($days / Constants::DAYS_IN_MY_YEAR) + 1;
		if ($my_year < 10) {
			$my_year = '0' . $my_year;
		}

		return ($difference->invert ? '-' : '') . $my_day . '.' . $my_year;
	}

	private $row_classes_for_states = array(
		'SATISFIED' => 'success',
		'NOT_SATISFIED' => 'danger',
		'CANCELED' => 'success'
	);
}
