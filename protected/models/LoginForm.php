<?php

class LoginForm extends CFormModel {
	public $password;

	public function rules() {
		return array(
			array('password', 'required'),
			array('password', 'validatePassword', 'skipOnError' => true)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль',
			'verify_code' => 'Тест Тьюринга'
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
