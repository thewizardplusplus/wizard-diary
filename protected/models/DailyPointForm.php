<?php

class DailyPointForm extends CFormModel {
	public $day;
	public $year;

	public function __construct($day, $year) {
		parent::__construct();

		$this->day = $day;
		$this->year = $year;
	}

	public function rules() {
		return array(
			array('day', 'required'),
			array(
				'day',
				'numerical',
				'min' => 1,
				'max' => Constants::DAYS_IN_MY_YEAR,
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно быть числом.',
				'tooSmall' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не меньше {min}.',
				'tooBig' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не больше {max}.'
			),
			array('year', 'required'),
			array(
				'year',
				'numerical',
				'min' => 1,
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно быть числом.',
				'tooSmall' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не меньше {min}.'
			)
		);
	}

	public function attributeLabels() {
		return array('day' => 'День', 'year' => 'Год');
	}
}
