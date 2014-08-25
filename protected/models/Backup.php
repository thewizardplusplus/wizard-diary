<?php

class Backup extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function getRowClassByCreateDuration($create_duration) {
		$maximal_execution_time = ini_get('max_execution_time');
		if ($maximal_execution_time === false) {
			return '';
		}

		$maximal_execution_time = intval($maximal_execution_time);
		if (
			$create_duration
			> Constants::BACKUPS_CREATE_HARD_LIMIT * $maximal_execution_time
		) {
			return 'danger';
		} else if (
			$create_duration
			> Constants::BACKUPS_CREATE_SOFT_LIMIT * $maximal_execution_time
		) {
			return 'warning';
		} else {
			return '';
		}
	}

	public function tableName() {
		return '{{backups}}';
	}
}
