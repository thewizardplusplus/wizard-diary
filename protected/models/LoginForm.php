<?php

Yii::setPathOfAlias(
	'JsonApi',
	Yii::getPathOfAlias('application.components.JsonApi')
);

class LoginForm extends CFormModel {
	public $password;
	public $verify_code;
	public $need_remember = true;

	public function rules() {
		return array(
			array('password', 'required'),
			array('password', 'validatePassword', 'skipOnError' => true),
			array('verify_code', 'verifyRecaptcha'),
			array('need_remember', 'boolean'),
			array('need_remember', 'default', 'value' => 1)
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

	public function verifyRecaptcha($attribute, $parameters) {
		try {
			$response = \JsonApi\Request::post(
				'https://www.google.com/recaptcha/api/siteverify',
				array(
					'secret' => Constants::RECAPTCHA_PRIVATE_KEY,
					'response' => $this->verify_code
				)
			);
			if (!$response->success) {
				throw new Exception('Тест Тьюринга не пройден.');
			}
		} catch (Exception $exception) {
			$this->addError('verify_code', $exception->getMessage());
		}
	}
}
