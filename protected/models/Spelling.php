<?php

class Spelling extends CActiveRecord {
	const WORD_PATTERN = '/\b[a-zа-яё]+\b/iu';

	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function tableName() {
		return '{{spellings}}';
	}

	public function rules() {
		return array(
			array('word', 'match', 'pattern' => Spelling::WORD_PATTERN),
			array('word', 'unique'),
			array('word', 'length', 'min' => 1)
		);
	}
}
