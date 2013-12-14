<?php

class Parameters extends CActiveRecord {
	const RECORD_ID =                   1;
	const DEFAULT_PASSWORD_HASH =
		'$2a$13$7RC2CWHDqafP4dvl7t5PCucccPVl7spVT4FiALXEaxWCnzCTskqAK';
	const MINIMUM_POINTS_ON_PAGE =      1;
	const MAXIMUM_POINTS_ON_PAGE =      100;
	const MINIMUM_VERSIONS_OF_BACKUPS = 1;
	const MAXIMUM_VERSIONS_OF_BACKUPS = 10;

	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function convertDateFromDatabaseToMyFormat($date) {
		return implode('.', array_reverse(explode('-', $date)));
	}

	public static function convertDateFromMyToDatabaseFormat($date) {
		return implode('-', array_reverse(explode('.', $date)));
	}

	public static function get() {
		$parameters = Parameters::model()->findByPk(Parameters::RECORD_ID);
		if (!is_null($parameters)) {
			return $parameters;
		} else {
			$parameters = new Parameters;
			$parameters->attributes = array('password_hash' => Parameters::
				DEFAULT_PASSWORD_HASH);
			$parameters->save();

			return $parameters;
		}
	}

	public function tableName() {
		return '{{parameters}}';
	}

	public function rules() {
		return array(
			array('id', 'default', 'value' => Parameters::RECORD_ID,
				'setOnEmpty' => FALSE),
			array('password_hash', 'required'),
			array('start_date', 'date', 'format' => 'yyyy-MM-dd'),
			array('points_on_page', 'numerical', 'min' => Parameters::
				MINIMUM_POINTS_ON_PAGE, 'max' => Parameters::
				MAXIMUM_POINTS_ON_PAGE),
			array('versions_of_backups', 'numerical', 'min' => Parameters::
				MINIMUM_VERSIONS_OF_BACKUPS, 'max' => Parameters::
				MAXIMUM_VERSIONS_OF_BACKUPS)
		);
	}
}
