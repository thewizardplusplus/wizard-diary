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
			array('text', 'required', 'on' => 'update'),
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

	private $row_classes_for_states = array(
		'SATISFIED' => 'success',
		'NOT_SATISFIED' => 'danger',
		'CANCELED' => 'success'
	);
}
