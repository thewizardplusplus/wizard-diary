<?php

class SessionGuard {
	public static function check() {
		if (Yii::app()->user->isGuest) {
			return;
		}

		$in_whitelist = UserInfo::model()->count(
			'`ip` = :ip AND `user_agent` = :user_agent',
			array(
				'ip' => Yii::app()->request->userHostAddress,
				'user_agent' => Yii::app()->request->userAgent
			)
		);
		if ($in_whitelist) {
			return;
		}

		Yii::app()->user->logout();
	}
}
