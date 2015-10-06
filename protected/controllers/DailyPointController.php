<?php

class DailyPointController extends CController {
	public function filters() {
		return array(
			'accessControl',
			'postOnly + create, update, delete',
			'ajaxOnly + create, update, delete'
		);
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider(
			'DailyPoint',
			array(
				'criteria' => array('order' => '`order`'),
				'pagination' => false
			)
		);
		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionCreate() {
		if (isset($_POST['DailyPoint'])) {
			$model = new DailyPoint();
			$model->attributes = $_POST['DailyPoint'];
			$result = $model->save();

			if ($result) {
				DailyPoint::renumberOrderFieldsForDate();
			}
		}
	}

	public function actionUpdate($id) {
		if (!isset($_POST['DailyPoint'])) {
			return;
		}

		$model = $this->loadModel($id);
		$model->attributes = $_POST['DailyPoint'];
		$result = $model->save();
		if (!$result) {
			return;
		}

		if (isset($_POST['DailyPoint']['text'])) {
			echo $model->text;
		}
		if (isset($_POST['DailyPoint']['order'])) {
			DailyPoint::renumberOrderFieldsForDate();
		}
	}

	public function actionOrder() {
		if (!isset($_POST['ids']) || !is_array($_POST['ids'])) {
			return;
		}

		$sql = '';
		$order = 3;
		foreach ($_POST['ids'] as $id) {
			if (!is_numeric($id)) {
				continue;
			}

			$sql .= sprintf(
				"UPDATE `{{daily_points}}` SET `order` = %d WHERE `id` = %d;\n",
				$order,
				intval($id)
			);
			$order += 2;
		}
		$sql = "START TRANSACTION;\n\n${sql}\nCOMMIT;\n";

		Yii::app()->db->createCommand($sql)->execute();
	}

	public function actionDelete($id) {
		$this->loadModel($id)->delete();
		DailyPoint::renumberOrderFieldsForDate();
	}

	private function loadModel($id) {
		$model = DailyPoint::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
