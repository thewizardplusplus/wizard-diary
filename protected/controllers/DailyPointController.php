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
		$current_date = date('Y-m-d');
		$my_current_date = DateFormatter::getDatePartsInMyFormat($current_date);
		$model = new DailyPointForm(
			$my_current_date->day,
			$my_current_date->year
		);

		if (isset($_POST['ajax']) and $_POST['ajax'] == 'daily-point-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['DailyPointForm'])) {
			$model->attributes = $_POST['DailyPointForm'];
			$result = $model->validate();
			if ($result) {
				$target_date = DateFormatter::getDateFromMyDateParts(
					$model->day,
					$model->year
				);

				DailyPointsAdder::addDailyPoints($target_date);
				$this->redirect(
					$this->createUrl('day/view', array('date' => $target_date))
				);
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
				'date' => $current_date,
				'my_date' => $my_current_date
			)
		);
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
