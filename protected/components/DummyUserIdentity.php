<?php

class DummyUserIdentity extends CUserIdentity {
	public function __construct() {
		parent::__construct('admin', '');
	}

	public function authenticate() {
		$this->errorCode = self::ERROR_NONE;
		return true;
	}
}
