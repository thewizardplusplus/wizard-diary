<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $session_lifetime_in_min;
	public $access_log_lifetime_in_s;
	public $use_whitelist = true;
	public $use_2fa = true;
	public $save_backups_to_dropbox = true;

	public function __construct() {
		parent::__construct();

		$this->session_lifetime_in_min =
			Parameters::getModel()->session_lifetime_in_min;
		$this->access_log_lifetime_in_s =
			Parameters::getModel()->access_log_lifetime_in_s;
		$this->use_whitelist = Parameters::getModel()->use_whitelist;
		$this->use_2fa = Parameters::getModel()->use_2fa;
		$this->save_backups_to_dropbox =
			Parameters::getModel()->save_backups_to_dropbox;
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
				'session_lifetime_in_min',
				'default',
				'value' => Constants::SESSION_LIFETIME_IN_MIN_DEFAULT
			),
			array(
				'session_lifetime_in_min',
				'numerical',
				'min' => Constants::SESSION_LIFETIME_IN_MIN_MINIMUM,
				'max' => Constants::SESSION_LIFETIME_IN_MIN_MAXIMUM,
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно быть числом.',
				'tooSmall' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не меньше {min}.',
				'tooBig' =>
					'Поле &laquo;{attribute}&raquo; должно быть '
						. 'не больше {max}.'
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
						. 'не больше {max}.'
			),
			array('use_whitelist', 'boolean'),
			array('use_whitelist', 'default', 'value' => 1),
			array('use_2fa', 'boolean'),
			array('use_2fa', 'default', 'value' => 1),
			array('save_backups_to_dropbox', 'boolean'),
			array('save_backups_to_dropbox', 'default', 'value' => 1)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль',
			'password_copy' => 'Пароль (копия)',
			'session_lifetime_in_min' => 'Время жизни сессии, мин',
			'access_log_lifetime_in_s' => 'Время жизни лога доступа, с',
			'use_whitelist' => 'Использовать белый список',
			'use_2fa' => 'Использовать 2FA',
			'save_backups_to_dropbox' => 'Сохранять бекапы в Dropbox'
		);
	}

	public function save() {
		$model = Parameters::getModel();
		if (!empty($this->password)) {
			$model->password_hash = CPasswordHelper::hashPassword(
				$this->password
			);
		}
		$model->session_lifetime_in_min = $this->session_lifetime_in_min;
		$model->access_log_lifetime_in_s = $this->access_log_lifetime_in_s;
		$model->use_whitelist = $this->use_whitelist;
		$model->use_2fa = $this->use_2fa;
		$model->save_backups_to_dropbox = $this->save_backups_to_dropbox;
		$model->save();
	}
}
