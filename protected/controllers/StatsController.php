<?php

class StatsController extends CController {
	public function filters() {
		return array('accessControl', 'ajaxOnly + dailyPoints, projects');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionList() {
		$this->render('list');
	}

	public function actionDailyPoints() {
		$this->renderPartial('daily_points');
	}

	public function actionProjects() {
		$this->renderPartial('projects');
	}
}
