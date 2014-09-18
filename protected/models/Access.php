<?php

class Access extends CActiveRecord {
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
		return '{{accesses}}';
	}

	public function getFormattedTimestamp() {
		$parts = explode(' ', $this->timestamp);
		return
			implode('.', array_reverse(explode('-', $parts[0])))
			. '&nbsp;'
			. $parts[1];
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
