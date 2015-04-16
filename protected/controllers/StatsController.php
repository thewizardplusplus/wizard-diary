<?php

class StatsController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionDailyPoints() {
		$this->render('daily_points');
	}

	public function actionProjects() {
		$this->render('projects');
	}
}
