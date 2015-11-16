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
		$my_date = $this->getCurrentDateInMyFormat();
		$model = new DailyPointForm($my_date->day, $my_date->year);

		if (isset($_POST['ajax']) and $_POST['ajax'] == 'daily-point-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['DailyPointForm'])) {
			$model->attributes = $_POST['DailyPointForm'];
			$result = $model->validate();
			if ($result) {
				Yii::log('daily point form validated', 'info');
			}
		}

		$day_container_class =
			count($model->getErrors('day'))
				? ' has-error'
				: '';
		$year_container_class =
			count($model->getErrors('year'))
				? ' has-error'
				: '';

		$data_provider = new CActiveDataProvider(
			'DailyPoint',
			array(
				'criteria' => array('order' => '`order`'),
				'pagination' => false
			)
		);

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'model' => $model,
				'day_container_class' => $day_container_class,
				'year_container_class' => $year_container_class,
				'my_date' => $my_date
			)
		);
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

	private function getCurrentDateInMyFormat() {
		$current_date = date('Y-m-d');
		$my_date = DateFormatter::formatMyDate($current_date);
		$my_date_parts = array_map('intval', explode('.', $my_date));

		$result = new stdClass;
		$result->day = $my_date_parts[0];
		$result->year = $my_date_parts[1];

		return $result;
	}
}
