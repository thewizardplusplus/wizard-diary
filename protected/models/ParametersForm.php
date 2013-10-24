<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $points_on_page;
	public $versions_of_backups;

	public function __construct($scenario = '') {
		parent::__construct($scenario);
		$this->points_on_page = Parameters::get()->points_on_page;
		$this->versions_of_backups = Parameters::get()->versions_of_backups;
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array('password_copy', 'compare', 'compareAttribute' => 'password'),
			array('points_on_page', 'numerical', 'min' => Parameters::
				MINIMUM_POINTS_ON_PAGE, 'max' => Parameters::
				MAXIMUM_POINTS_ON_PAGE),
			array('versions_of_backups', 'numerical', 'min' => Parameters::
				MINIMUM_VERSIONS_OF_BACKUPS, 'max' => Parameters::
				MAXIMUM_VERSIONS_OF_BACKUPS)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):',
			'points_on_page' => 'Пунктов на страницу:',
			'versions_of_backups' => 'Версий бекапов:'
		);
	}

	public function getParameters() {
		$attributes = array(
			'points_on_page' => $this->points_on_page,
			'versions_of_backups' => $this->versions_of_backups
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
