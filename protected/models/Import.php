<?php

class Import extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function tableName() {
		return '{{imports}}';
	}

	public function rules() {
		return array(array('points_description', 'safe'));
	}

	public function attributeLabels() {
		return array('points_description' => 'Описание пунктов');
	}

	public function getFormattedDate() {
		return implode('.', array_reverse(explode('-', $this->date)));
	}
}
