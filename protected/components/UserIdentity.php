<?php

class UserIdentity extends CUserIdentity {
	public function __construct($password) {
		parent::__construct('admin', $password);
	}

	public function authenticate() {
		if (CPasswordHelper::verifyPassword(
			$this->password,
			Parameters::getModel()->password_hash
		)) {
			$this->errorCode = self::ERROR_NONE;
		} else {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}

		return $this->errorCode == self::ERROR_NONE;
	}
}
