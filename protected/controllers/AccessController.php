<?php

class AccessController extends CController {
	public static function accessProcess() {
		$user_ip = Yii::app()->request->userHostAddress;
		$login_count = Access::model()->countBySql(
			'SELECT MAX(counted)'
			. 'FROM ('
				. 'SELECT COUNT(*) AS counted '
				. 'FROM {{accesses}} '
				. 'WHERE url = :url AND method = :method AND ip = :ip '
				. 'GROUP BY ROUND(timestamp / :time_window)'
			. ') counts',
			array(
				'url' => Yii::app()->createUrl('site/login'),
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
		return array('accessControl');
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
		$this->render('list', array('data_provider' => $data_provider));
	}
}
