<?php

class DailyPoint extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function renumberOrderFieldsForDate() {
		$sql =
			"START TRANSACTION;\n"
			. "\n"
			. "SET @order = 1;\n"
			. "UPDATE {{daily_points}}\n"
			. "SET `order` = (@order := @order + 2)\n"
			. "ORDER BY `order`, `id`;\n"
			. "\n"
			. "COMMIT;";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function tableName() {
		return '{{daily_points}}';
	}

	public function rules() {
		return array(
			array('text', 'safe'),
			array('order', 'numerical')
		);
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			if (!empty($this->text) and substr($this->text, -1) == ';') {
				$this->text = substr($this->text, 0, -1);
			}
		}

		return $result;
	}
}
