<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $points_on_page;
	public $access_log_lifetime_in_s;

	public function __construct() {
		parent::__construct();
		$this->points_on_page = Parameters::getModel()->points_on_page;
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
				'points_on_page',
				'default',
				'value' => Constants::POINTS_ON_PAGE_DEFAULT
			),
			array(
				'points_on_page',
				'numerical',
				'min' => Parameters::POINTS_ON_PAGE_MINIMUM,
				'max' => Parameters::POINTS_ON_PAGE_MAXIMUM,
				'message' => '{attribute} должно быть числом.',
				'tooSmall' => '{attribute} должно быть не меньше {min}.',
				'tooBig' => '{attribute} должно быть не больше {max}.',
			),
			array(
				'access_log_lifetime_in_s',
				'default',
				'value' => Constants::ACCESS_LOG_LIFETIME_IN_S_DEFAULT
			),
			array(
				'access_log_lifetime_in_s',
				'numerical',
				'min' => Parameters::ACCESS_LOG_LIFETIME_IN_S_MINIMUM,
				'max' => Parameters::ACCESS_LOG_LIFETIME_IN_S_MAXIMUM,
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
			'points_on_page' => 'Число пунктов на странице',
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
		$model->points_on_page = $this->points_on_page;
		$model->access_log_lifetime_in_s = $this->access_log_lifetime_in_s;
		$model->save();
	}
}
