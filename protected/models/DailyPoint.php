<?php

class DailyPoint extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
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
}
