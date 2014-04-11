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
			array('check', 'boolean', 'falseValue' => 0, 'trueValue' => 1),
			array('order', 'numerical')
		);
	}
}
