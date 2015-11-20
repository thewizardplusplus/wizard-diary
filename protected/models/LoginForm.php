<?php

class LoginForm extends CFormModel {
	public $password;
	public $need_remember = true;

	public function rules() {
		return array(
			array('password', 'required'),
			array('password', 'validatePassword', 'skipOnError' => true),
			array('need_remember', 'boolean')
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль',
			'verify_code' => 'Тест Тьюринга',
			'need_remember' => 'Запомнить'
		);
	}

	public function validatePassword() {
		if (
			!CPasswordHelper::verifyPassword(
				$this->password,
				Parameters::getModel()->password_hash
			)
		) {
			$this->addError('password', 'Неверный пароль.');
		}
	}
}
