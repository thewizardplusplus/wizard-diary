<?php

class AccessLogRecord extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function __construct() {
		parent::__construct();

		$this->ip = Yii::app()->request->userHostAddress;
		$this->user_agent = Yii::app()->request->userAgent;
		$this->method = Yii::app()->request->requestType;
		$this->url = Yii::app()->request->url;
	}

	public function tableName() {
		return '{{access_log}}';
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			// disable updating
			$result = $this->isNewRecord;
		}

		return $result;
	}
}
