<?php

class AccessController extends CController {
	public static function accessProcess() {
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
					'order' => 'ip, user_agent, timestamp DESC'
				),
				'sort' => false
			)
		);
		$this->render('list', array('data_provider' => $data_provider));
	}
}
