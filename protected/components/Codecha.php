<?php

class Codecha {
	const CODECHA_URL = 'http://codecha.org/api/verify';
	const CODECHA_PUBLIC_KEY = 'b06bf77ee2a0463091b4603d6121a518';
	const CODECHA_PRIVATE_KEY = '43950b9f9a2f445199ba4ab43c56fcef';

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
				'privatekey' => self::CODECHA_PRIVATE_KEY
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
