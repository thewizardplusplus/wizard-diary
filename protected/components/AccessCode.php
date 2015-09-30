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
		if (empty(Constants::ACCESS_CODE_TARGETS)) {
			throw new CException('Список назначений для кода доступа пуст.');
		}

		$targets = explode('|', Constants::ACCESS_CODE_TARGETS);
		$targets = array_map('trim', $targets);

		$counter = 0;
		$access_code = self::generate();
		foreach ($targets as $target) {
			if (empty($target)) {
				throw new CException(
					"Назначение #$counter для кода доступа пусто."
				);
			}

			switch ($target) {
				case 'sms':
					self::sendSms($access_code);
					break;
				case 'email':
					self::sendEmail($access_code);
					break;
				case 'log':
					self::printToLog($access_code);
					break;
				default:
					throw new CException(
						"Недопустимое назначение #$counter "
						. "(&laquo;$target&raquo;) для кода доступа."
					);

					break;
			}

			$counter++;
		}

		self::set($access_code);
	}

	public static function sendSms($access_code) {
		$sms_sender = new \Zelenin\smsru(
			null,
			Constants::SMS_RU_LOGIN,
			Constants::SMS_RU_PASSWORD
		);
		$result = $sms_sender->auth_check();
		if ($result['code'] != 100) {
			throw new CException(
				'Ошибка отправки кода доступа в SMS. ' . $result['description']
			);
		}

		$result = $sms_sender->sms_send(Constants::SMS_RU_LOGIN, $access_code);
		if ($result['code'] != 100) {
			throw new CException(
				'Ошибка отправки кода доступа в SMS. ' . $result['description']
			);
		}
	}

	public static function sendEmail($access_code) {
		$headers =
			"From: " . Constants::ACCESS_CODE_EMAIL_FROM . "\r\n"
			. "Reply-To: <>\r\n"
			. "MIME-Version: 1.0\r\n"
			. "Content-Type: text/plain; charset=utf-8\r\n";
		$result = mail(
			Constants::ACCESS_CODE_EMAIL_TO,
			'Access code',
			$access_code,
			$headers
		);
		if (!$result) {
			throw new CException('Ошибка отправки кода доступа в Email.');
		}
	}

	public static function printToLog($access_code) {
		Yii::log("access code: $access_code");
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
