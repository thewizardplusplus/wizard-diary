<?php

class DailyPoint extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function renumberOrderFieldsForDate() {
		Yii::app()->db->createCommand('SET @order = 1')->execute();
		DailyPoint::model()->updateAll(
			// new values
			array('order' => new CDbExpression('(@order := @order + 2)')),
			// sorting
			array('order' => '`order`')
		);
	}

	public function tableName() {
		return '{{daily_points}}';
	}

	public function rules() {
		return array(
			array('text', 'safe'),
			array('order', 'numerical')
		);
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			if (!empty($this->text) and substr($this->text, -1) == ';') {
				$this->text = substr($this->text, 0, -1);
			}
		}

		return $result;
	}
}
