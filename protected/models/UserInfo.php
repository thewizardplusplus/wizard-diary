<?php

class UserInfo extends CActiveRecord {
	public $number;

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

	public function isActual() {
		$difference_in_min =
			(
				date_create()->getTimestamp()
				- date_create($this->timestamp)->getTimestamp()
			)
			/ 60;
		return
			$difference_in_min
			< Parameters::getModel()->session_lifetime_in_min;
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
