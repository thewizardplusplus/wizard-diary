<?php

class Codecha {
	const CODECHA_URL = 'http://codecha.org/api/verify';

	public static function check(
		$challenge,
		$response,
		$remote_ip
	) {
		$field_string = http_build_query(
			array(
				'challenge' => $challenge,
				'response' => $response,
				'remoteip' => $remote_ip,
				'privatekey' => Constants::CODECHA_PRIVATE_KEY
			)
		);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, self::CODECHA_URL);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $field_string);
		$result = curl_exec($curl);

		return strtok(trim($result), "\n") == 'true';
	}
}
