<?php

class DailyPointForm extends CFormModel {
	public $day;
	public $year;

	public function __construct($day, $year) {
		parent::__construct();

		$this->day = $day;
		$this->maximal_day = $day;
		$this->year = $year;
		$this->maximal_year = $year;
	}

	public function rules() {
		return array(
			array('day', 'required'),
			array(
				'day',
				'numerical',
				'min' => 1,
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно быть числом.',
				'tooSmall' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не меньше {min}.'
			),
			array('day', 'validateDayTopLimit', 'skipOnError' => true),
			array('year', 'required'),
			array(
				'year',
				'numerical',
				'min' => 1,
				'max' => $this->maximal_year,
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно быть числом.',
				'tooSmall' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не меньше {min}.',
				'tooBig' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не больше {max}.'
			)
		);
	}

	public function attributeLabels() {
		return array('day' => 'День', 'year' => 'Год');
	}

	public function validateDayTopLimit() {
		$day_top_limit = Constants::DAYS_IN_MY_YEAR;
		if ($this->year == $this->maximal_year) {
			$day_top_limit = $this->maximal_day;
		}

		if ($this->day > $day_top_limit) {
			$this->addError(
				'day',
				sprintf(
					'Поле &laquo;%s&raquo; должно быть не больше %d '
						. '(для выбранного года).',
					$this->getAttributeLabel('day'),
					$day_top_limit
				)
			);
		}
	}

	private $maximal_day;
	private $maximal_year;
}
