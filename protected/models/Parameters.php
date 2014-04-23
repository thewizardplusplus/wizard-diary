<?php

class Parameters extends CActiveRecord {
	const RECORD_ID = 1;

	public static function model() {
		return parent::model(__CLASS__);
	}

	public static function getModel() {
		$model = Parameters::model()->findByPk(Parameters::RECORD_ID);
		if (is_null($model)) {
			$model = new Parameters();
			$model->password_hash = CPasswordHelper::hashPassword(
				Constants::DEFAULT_PASSWORD
			);
			$model->save();
		}

		return $model;
	}

	public function tableName() {
		return '{{parameters}}';
	}

	public function rules() {
		return array(
			array(
				'id',
				'default',
				'value' => self::RECORD_ID,
				// the attribute will always be assigned with the default value,
				// even if it is already explicitly assigned a value
				'setOnEmpty' => false
			),
			array('password_hash', 'required')
		);
	}
}
