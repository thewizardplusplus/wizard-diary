<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $start_date;
	public $points_on_page;

	public function __construct($scenario = '') {
		parent::__construct($scenario);

		$this->start_date = Parameters::convertDateFromDatabaseToMyFormat(
			Parameters::get()->start_date);
		$this->points_on_page = Parameters::get()->points_on_page;
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array('password_copy', 'compare', 'compareAttribute' => 'password'),
			array('start_date', 'date', 'format' => 'dd.MM.yyyy'),
			array('points_on_page', 'numerical', 'min' => Parameters::
				MINIMUM_POINTS_ON_PAGE, 'max' => Parameters::
				MAXIMUM_POINTS_ON_PAGE)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):',
			'start_date' => 'Дата начала:',
			'points_on_page' => 'Пунктов на страницу:'
		);
	}

	public function getParameters() {
		$attributes = array(
			'start_date' => Parameters::convertDateFromMyToDatabaseFormat($this
				->start_date),
			'points_on_page' => $this->points_on_page
		);
		if (!empty($this->password)) {
			$attributes['password_hash'] = CPasswordHelper::hashPassword($this->
				password);
		}

		$parameters = Parameters::get();
		$parameters->attributes = $attributes;

		return $parameters;
	}
}
