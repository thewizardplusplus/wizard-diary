<?php

require_once('sms-ru/src/smsru.php');

class AccessCode {
	public static function isSetted() {
		return !is_null(Yii::app()->session['ACCESS_CODE']);
	}

	public static function verify($access_code) {
		return
			self::isSetted()
			&& Yii::app()->session['ACCESS_CODE'] == $access_code;
	}

	public static function getRemainingLifetime() {
		return
			!is_null(Yii::app()->session['ACCESS_CODE_SETTING_TIME'])
				? Constants::ACCESS_CODE_LIFETIME_IN_S
					- (time() - Yii::app()->session['ACCESS_CODE_SETTING_TIME'])
				: 0;
	}

	public static function send() {
		$sms_sender = new \Zelenin\smsru(
			null,
			Constants::SMS_RU_LOGIN,
			Constants::SMS_RU_PASSWORD
		);
		$result = $sms_sender->auth_check();
		if ($result['code'] != 100) {
			throw new CException(
				'Ошибка отправки кода доступа. ' . $result['description']
			);
		}

		$access_code = self::generate();
		$result = $sms_sender->sms_send(Constants::SMS_RU_LOGIN, $access_code);
		if ($result['code'] != 100) {
			throw new CException(
				'Ошибка отправки кода доступа. ' . $result['description']
			);
		}

		self::set($access_code);
	}

	public static function cleanIfNeed() {
		if (self::getRemainingLifetime() <= 0) {
			self::clean();
		}
	}

	public static function clean() {
		unset(Yii::app()->session['ACCESS_CODE']);
		unset(Yii::app()->session['ACCESS_CODE_SETTING_TIME']);
	}

	private static function generate() {
		$access_code = '';
		for ($i = 0; $i < Constants::ACCESS_CODE_LENGTH; $i++) {
			$access_code .= mt_rand(0, 9);
		}

		return $access_code;
	}

	private static function set($access_code) {
		Yii::app()->session['ACCESS_CODE'] = $access_code;
		Yii::app()->session['ACCESS_CODE_SETTING_TIME'] = time();
	}
}
