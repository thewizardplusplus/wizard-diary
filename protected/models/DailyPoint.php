<?php

class DailyPoint extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function renumberOrderFieldsForDate() {
		Yii::app()->db->createCommand('SET @order = 1')->execute();
		DailyPoint::model()->updateAll(
			// new values
			array('order' => new CDbExpression('(@order := @order + 2)')),
			// sorting
			array('order' => '`order`')
		);
	}

	public function tableName() {
		return '{{daily_points}}';
	}

	public function rules() {
		return array(
			array('text', 'safe'),
			array('check', 'boolean', 'falseValue' => 0, 'trueValue' => 1),
			array('order', 'numerical')
		);
	}

	public function getRealText() {
		$text = $this->text;
		if (!empty($text) and substr($text, -1) == ';') {
			$text = substr($text, 0, -1);
		}

		return CHtml::encode($text);
	}

	public function getFormattedText() {
		$text = $this->getRealText();
		$text = preg_replace(
			'/^([^,]+,)\s*(.+)$/',
			'<strong>$1</strong><br />$2',
			$text
		);
		if (!empty($text)) {
			$text .= ';';
		}
		$text = str_replace('&quot;', '"', $text);
		$text = preg_replace(
			'/"([^"]*)"/',
			'&laquo;$1&raquo;',
			$text
		);
		$text = str_replace('"', '&quot;', $text);
		$text = preg_replace('/\s-\s/', ' &mdash; ', $text);

		return $text;
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			if (!$this->isNewRecord and empty($this->text)) {
				$this->check = 0;
			}
			if (!empty($this->text) and substr($this->text, -1) != ';') {
				$this->text .= ';';
			}
		}

		return $result;
	}
}
