<?php

class Spelling extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function tableName() {
		return '{{spellings}}';
	}

	public function rules() {
		return array(
			array('word', 'match', 'pattern' => '/[а-яё]+/iu'),
			array('word', 'unique'),
			array('word', 'length', 'min' => 1)
		);
	}
}
