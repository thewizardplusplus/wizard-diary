<?php

class Point extends CActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{points}}';
	}

	public function rules() {
		return array(
			array('text', 'safe'),
			array('text', 'required', 'on' => 'update'),
			array('state', 'in', 'range' => array('INITIAL', 'SATISFIED',
				'NOT_SATISFIED', 'CANCELED'), 'on' => 'update'),
			array('check', 'boolean', 'trueValue' => 1, 'falseValue' => 0)
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
}
