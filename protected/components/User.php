<?php

class User extends CWebUser {
	protected function afterLogin($from_cookie) {
		$result = parent::beforeLogout();
		if ($result === false) {
			return false;
		}

		Yii::log('login 1', 'info');
		return true;
	}
}
