<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $access_log_lifetime_in_s;

	public function __construct() {
		parent::__construct();
		$this->access_log_lifetime_in_s =
			Parameters::getModel()->access_log_lifetime_in_s;
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array(
				'password_copy',
				'compare',
				'compareAttribute' => 'password',
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно в точности '
						. 'повторять поле &laquo;{compareAttribute}&raquo;.'
			),
			array(
				'access_log_lifetime_in_s',
				'default',
				'value' => Constants::ACCESS_LOG_LIFETIME_IN_S_DEFAULT
			),
			array(
				'access_log_lifetime_in_s',
				'numerical',
				'min' => Constants::ACCESS_LOG_LIFETIME_IN_S_MINIMUM,
				'max' => Constants::ACCESS_LOG_LIFETIME_IN_S_MAXIMUM,
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно быть числом.',
				'tooSmall' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не меньше {min}.',
				'tooBig' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не больше {max}.',
			)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль',
			'password_copy' => 'Пароль (копия)',
			'access_log_lifetime_in_s' => 'Время жизни лога доступа, с',
		);
	}

	public function save() {
		$model = Parameters::getModel();
		if (!empty($this->password)) {
			$model->password_hash = CPasswordHelper::hashPassword(
				$this->password
			);
		}
		$model->access_log_lifetime_in_s = $this->access_log_lifetime_in_s;
		$model->save();
	}
}
