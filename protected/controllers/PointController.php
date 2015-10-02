<?php

class PointController extends CController {
	public function filters() {
		return array('accessControl', 'postOnly + update', 'ajaxOnly + update');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionUpdate($id) {
		if (!isset($_POST['Point'])) {
			return;
		}

		$model = $this->loadModel($id);
		$model->attributes = $_POST['Point'];
		$model->save();
	}

	public function actionAddDailyPoints() {
		$date = DailyPointsAdder::addDailyPoints();
		$this->redirect($this->createUrl('day/view', array('date' => $date)));
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
