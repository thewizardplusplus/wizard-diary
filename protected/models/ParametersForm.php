<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $start_date;

	public function __construct() {
		parent::__construct();
		$this->start_date = self::convertDateFromDatabaseToMyFormat(
			Parameters::getModel()->start_date
		);
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array('password_copy', 'compare', 'compareAttribute' => 'password'),
			array('start_date', 'date', 'format' => 'dd.MM.yyyy')
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):',
			'start_date' => 'Дата начала:'
		);
	}

	public function save() {
		$model = Parameters::getModel();
		if (!empty($this->password)) {
			$model->password_hash = CPasswordHelper::hashPassword(
				$this->password
			);
		}
		$model->start_date = self::convertDateFromMyToDatabaseFormat(
			$this->start_date
		);
		$model->save();
	}

	private static function convertDateFromDatabaseToMyFormat($date) {
		return implode('.', array_reverse(explode('-', $date)));
	}

	private static function convertDateFromMyToDatabaseFormat($date) {
		return implode('-', array_reverse(explode('.', $date)));
	}
}
