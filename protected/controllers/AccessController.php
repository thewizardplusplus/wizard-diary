<?php

class AccessController extends CController {
	public static function accessProcess() {
		$user_ip = Yii::app()->request->userHostAddress;
		$login_count = Access::model()->countBySql(
			'SELECT MAX(counted)'
			. 'FROM ('
				. 'SELECT COUNT(*) AS counted '
				. 'FROM {{accesses}} '
				. 'WHERE (url LIKE :login_url OR url LIKE :access_code_url)'
					. 'AND method = :method '
					. 'AND ip = :ip '
				. 'GROUP BY ROUND(timestamp / :time_window)'
			. ') counts',
			array(
				'login_url' =>
					self::escapeForLike(
						Yii::app()->createUrl('site/login')
					)
					. '%',
				'access_code_url' =>
					self::escapeForLike(
						Yii::app()->createUrl('site/accessCode')
					)
					. '%',
				'method' => 'POST',
				'ip' => $user_ip,
				'time_window' => Constants::LOGIN_LIMIT_TIME_WINDOW_IN_S
			)
		);
		if ($login_count > Constants::LOGIN_LIMIT_MAXIMAL_COUNT) {
			throw new CException('Твой IP (' . $user_ip . ') забанен.');
		}

		$access_log_record = new Access();
		$access_log_record->save();
	}

	public function filters() {
		return array(
			'accessControl',
			'ajaxOnly + decodeIp, decodeUserAgent, info'
		);
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$get_banned_ips_command = Yii::app()->db->createCommand(
			'SELECT ip '
			. 'FROM ('
				. 'SELECT ip, MAX(counted) AS counter '
				. 'FROM ('
					. 'SELECT ip, COUNT(*) AS counted '
					. 'FROM {{accesses}} '
					. 'WHERE (url LIKE :login_url OR url LIKE :access_code_url)'
						. 'AND method = :method '
					. 'GROUP BY ip, ROUND(timestamp / :time_window)'
				. ') counts '
				. 'GROUP BY ip'
			. ') conteds '
			. 'WHERE counter > :maximal_counter'
		);
		$get_banned_ips_command->bindValues(
			array(
				'login_url' =>
					self::escapeForLike(
						Yii::app()->createUrl('site/login')
					)
					. '%',
				'access_code_url' =>
					self::escapeForLike(
						Yii::app()->createUrl('site/accessCode')
					)
					. '%',
				'method' => 'POST',
				'time_window' => Constants::LOGIN_LIMIT_TIME_WINDOW_IN_S,
				'maximal_counter' => Constants::LOGIN_LIMIT_MAXIMAL_COUNT
			)
		);
		$wrapped_banned_ips = $get_banned_ips_command->queryAll();

		$banned_ips = array_map(
			function($item) {
				return Yii::app()->db->quoteValue($item['ip']);
			},
			$wrapped_banned_ips
		);

		$data_provider = new CActiveDataProvider(
			'Access',
			array(
				'criteria' => array(
					'select' =>
						'ip,'
						. 'user_agent,'
						. 'MAX(timestamp) AS timestamp'
						. (!empty($banned_ips)
							? ', ip IN (' . implode(', ', $banned_ips) . ')'
								. 'AS banned'
							: '') . ','
						. 'COUNT(*) AS number',
					'group' => 'ip, user_agent',
					'order' => 'timestamp DESC'
				),
				'sort' => false
			)
		);

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionWhitelist() {
		$data_provider = new CActiveDataProvider(
			'UserInfo',
			array(
				'criteria' => array('order' => 'timestamp DESC'),
				'sort' => false,
				'pagination' => false
			)
		);

		$this->render('whitelist', array('data_provider' => $data_provider));
	}

	public function actionDecodeIp($ip) {
		$answer = file_get_contents(
			'http://ipinfo.io/'
			. ($ip != '127.0.0.1' ? $ip . '/' : '' )
			. 'geo'
		);
		echo !empty($answer) ? $answer : 'null';
	}

	public function actionDecodeUserAgent($user_agent) {
		$answer = file_get_contents(
			'http://useragentstring.com/?uas=' . $user_agent
			. '&getJSON='
				. 'agent_type'
				. '-agent_name'
				. '-agent_version'
				. '-os_type'
				. '-os_name'
				. '-os_versionName'
				. '-os_versionNumber'
				. '-linux_distibution'
		);
		echo !empty($answer) ? $answer : 'null';
	}

	public function actionInfo() {
		$info = array(
			'counter' => Access::model()->count(''),
			'speed' => array(
				'by_day' => Access::model()->count(
					'timestamp >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY)'
				),
				'by_hour' => Access::model()->count(
					'timestamp >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 HOUR)'
				),
				'by_minute' => Access::model()->count(
					'timestamp'
					. '>= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MINUTE)'
				)
			)
		);
		$json = json_encode($info, JSON_NUMERIC_CHECK);

		echo $json;
	}

	private static function escapeForLike($value) {
		return preg_replace('/(_|%)/', '\\\\$1', $value);
	}
}
