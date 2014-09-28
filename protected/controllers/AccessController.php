<?php

class AccessController extends CController {
	public static function accessProcess() {
		$user_ip = Yii::app()->request->userHostAddress;
		$login_count = Access::model()->countBySql(
			'SELECT MAX(counted)'
			. 'FROM ('
				. 'SELECT COUNT(*) AS counted '
				. 'FROM {{accesses}} '
				. 'WHERE (url = :login_url OR url = :access_code_url)'
					. 'AND method = :method '
					. 'AND ip = :ip '
				. 'GROUP BY ROUND(timestamp / :time_window)'
			. ') counts',
			array(
				'login_url' => Yii::app()->createUrl('site/login'),
				'access_code_url' => Yii::app()->createUrl('site/accessCode'),
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
		return array('accessControl', 'ajaxOnly + decodeIp, decodeUserAgent');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider(
			'Access',
			array(
				'criteria' => array(
					'select' => 'ip, user_agent, MAX(timestamp) AS timestamp',
					'group' => 'ip, user_agent',
					'order' => 'timestamp DESC'
				),
				'sort' => false
			)
		);

		$get_counts_command = Yii::app()->db->createCommand(
			'SELECT ip, MAX(counted) AS count '
			. 'FROM ('
				. 'SELECT ip, COUNT(*) AS counted '
				. 'FROM {{accesses}} '
				. 'WHERE (url = :login_url OR url = :access_code_url)'
					. 'AND method = :method '
				. 'GROUP BY ROUND(timestamp / :time_window)'
			. ') counts'
		);
		$get_counts_command->bindValues(
			array(
				'login_url' => Yii::app()->createUrl('site/login'),
				'access_code_url' => Yii::app()->createUrl('site/accessCode'),
				'method' => 'POST',
				'time_window' => Constants::LOGIN_LIMIT_TIME_WINDOW_IN_S
			)
		);
		$counts = $get_counts_command->queryAll();

		$this->render(
			'list',
			array('data_provider' => $data_provider, 'counts' => $counts)
		);
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
}
