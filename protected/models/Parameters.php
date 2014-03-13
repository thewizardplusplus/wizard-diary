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
				'value' => Parameters::RECORD_ID,
				'setOnEmpty' => false
			),
			array('password_hash', 'required')
		);
	}
}
