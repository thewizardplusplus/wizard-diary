<?php

class Point extends CActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public static function getOrderBound($bound, $date) {
		$criteria = new CDbCriteria;
		if ($bound == 'minimal') {
			$criteria->select = 'MIN(`order`) AS "order"';
		} else if ($bound == 'maximal') {
			$criteria->select = 'MAX(`order`) AS "order"';
		} else {
			return 0;
		}
		$criteria->compare('date', $date);

		$model = Point::model()->find($criteria);
		if (is_null($model)) {
			return 0;
		}

		return intval($model->order);
	}

	public function tableName() {
		return '{{points}}';
	}

	public function rules() {
		return array(
			array('state', 'in', 'range' => array('INITIAL', 'SATISFIED',
				'NOT_SATISFIED', 'CANCELED'), 'on' => 'update'),
			array('text', 'safe'),
			array('text', 'required', 'on' => 'update')
		);
	}

	public function attributeLabels() {
		return array('text' => 'Текст:');
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			if ($this->isNewRecord) {
				$date = date("Y-m-d");
				$this->date = $date;
				$this->order = Point::getOrderBound('maximal', $date) + 1;
			}

			return true;
		} else {
			return false;
		}
	}
}
