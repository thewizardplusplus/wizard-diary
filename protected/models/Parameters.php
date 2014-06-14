<?php

class Parameters extends CActiveRecord {
	const RECORD_ID = 1;
	const POINTS_ON_PAGE_MINIMUM = 1;
	const POINTS_ON_PAGE_MAXIMUM = 255;
	const DROPBOX_ACCESS_TOKEN_LENGTH_MAXIMUM = 255;

	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function getModel() {
		$model = Parameters::model()->findByPk(Parameters::RECORD_ID);
		if (is_null($model)) {
			$model = new Parameters();
			$model->id = self::RECORD_ID;
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
}
