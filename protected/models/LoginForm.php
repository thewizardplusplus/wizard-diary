<?php

class LoginForm extends CFormModel {
	public $password;
	public $remember_me = false;
	public $verify_code;

	public function rules() {
		return array(
			array('password', 'required'),
			array('password', 'authenticate'),
			array('remember_me', 'boolean'),
			array('verify_code', 'captcha', 'caseSensitive' => true)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'remember_me' => 'Запомнить',
			'verify_code' => 'Код проверки:'
		);
	}

	public function authenticate($attribute) {
		if (!$this->hasErrors()) {
			$this->identity = new UserIdentity($this->password);
			$this->identity->authenticate();

			if ($this->identity->errorCode != UserIdentity::ERROR_NONE) {
				$this->addError('password', 'Неверный пароль.');
			}
		}
	}

	public function login() {
		if (is_null($this->identity)) {
			$this->identity = new UserIdentity($this->password);
			$this->identity->authenticate();
		}

		if ($this->identity->errorCode == UserIdentity::ERROR_NONE) {
			return Yii::app()->user->login(
				$this->identity,
				$this->remember_me ? Constants::REMEMBER_DURATION_IN_S : 0
			);
		} else {
			return false;
		}
	}

	private $identity;
}
