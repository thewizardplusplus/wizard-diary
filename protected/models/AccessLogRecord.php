<?php

class AccessLogRecord extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function __construct() {
		parent::__construct();

		$this->ip = Yii::app()->request->userHostAddress;
		$this->user_agent = Yii::app()->request->userAgent;
	}

	public function tableName() {
		return '{{access_log}}';
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			$result = $this->isNewRecord;
		}

		return $result;
	}
}
