<?php

class Point extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public static function renumberOrderFieldsForDate($date) {
		Yii::app()->db->createCommand('SET @order = 1')->execute();
		Point::model()->updateAll(
			array('order' => new CDbExpression('(@order := @order + 2)')),
			array(
				'condition' => 'date = "' . $date . '"',
				'order' => '`order`, id'
			)
		);
	}

	public function tableName() {
		return '{{points}}';
	}

	public function rules() {
		return array(
			array('text', 'safe'),
			array(
				'state',
				'in',
				'range' => array(
					'INITIAL',
					'SATISFIED',
					'NOT_SATISFIED',
					'CANCELED'
				)
			),
			array('order', 'numerical')
		);
	}

	public function getStateClass() {
		return strtolower(str_replace('_', '-', $this->state));
	}

	public function getRowClassByState() {
		return 'point-row point-' . $this->id . ' '
			. (!$this->daily
				? self::$row_classes_for_states[$this->state]
				: 'warning');
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
			if (!empty($this->text) and substr($this->text, -1) == ';') {
				$this->text = substr($this->text, 0, -1);
			}

			if ($this->isNewRecord) {
				$this->date = date('Y-m-d');
			}
			if (empty($this->text)) {
				$this->state = 'INITIAL';
			}
		}

		return $result;
	}

	private static $row_classes_for_states = array(
		'INITIAL' => '',
		'SATISFIED' => 'success',
		'NOT_SATISFIED' => 'danger',
		'CANCELED' => 'success'
	);
}
