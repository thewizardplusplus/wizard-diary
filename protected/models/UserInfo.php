<?php

class UserInfo extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function tableName() {
		return '{{whitelist}}';
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

			$this->ip = Yii::app()->request->userHostAddress;
			$this->user_agent = Yii::app()->request->userAgent;
		}

		return $result;
	}
}
