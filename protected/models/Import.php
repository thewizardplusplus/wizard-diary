<?php

class Import extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function tableName() {
		return '{{imports}}';
	}

	public function rules() {
		return array(
			array('date', 'required'),
			array(
				'date',
				'date',
				'format' => 'yyyy-dd-MM',
				'skipOnError' => true
			),
			array(
				'date',
				'unique',
				'message' => '{attribute} &laquo;{value}&raquo; уже занята.'
			),
			array('points_description', 'required')
		);
	}

	public function attributeLabels() {
		return array(
			'date' => 'Целевая дата',
			'points_description' => 'Описание пунктов'
		);
	}
}
