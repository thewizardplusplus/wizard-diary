<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;

	public function rules() {
		return array(
			array('password', 'required'),
			array('password_copy', 'required'),
			array('password_copy', 'compare', 'compareAttribute' => 'password')
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):'
		);
	}

	public function save() {
		$model = Parameters::getModel();
		if (!empty($this->password)) {
			$model->password_hash = CPasswordHelper::hashPassword(
				$this->password
			);
		}
		$model->save();
	}
}
