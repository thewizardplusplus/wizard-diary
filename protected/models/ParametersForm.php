<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $points_on_page;

	public function __construct($scenario = '') {
		parent::__construct($scenario);
		$this->points_on_page = Parameters::get()->points_on_page;
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array('password_copy', 'compare', 'compareAttribute' => 'password'),
			array('points_on_page', 'numerical', 'min' => Parameters::
				MINIMUM_POINTS_ON_PAGE, 'max' => Parameters::
				MAXIMUM_POINTS_ON_PAGE)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):',
			'points_on_page' => 'Пунктов на страницу:',
		);
	}

	public function getParameters() {
		$attributes = array('points_on_page' => $this->points_on_page);
		if (!empty($this->password)) {
			$attributes['password_hash'] = CPasswordHelper::hashPassword($this->
				password);
		}

		$parameters = Parameters::get();
		$parameters->attributes = $attributes;

		return $parameters;
	}
}
