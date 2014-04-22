<?php

class PointController extends CController {
	public function filters() {
		return array(
			'accessControl',
			'postOnly + create, update, delete',
			'ajaxOnly + create, update, delete'
		);
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider(
			'Point',
			array(
				'criteria' => array('order' => 'date, `order`'),
				'pagination' => array('pagesize' => Constants::POINTS_ON_PAGE)
			)
		);

		if (!isset($_GET['ajax']) or $_GET['ajax'] != 'point-list') {
			$pagination = $data_provider->pagination;
			$pagination->setItemCount($data_provider->getTotalItemCount());
			$pagination->currentPage = $pagination->pageCount - 1;
		}

		$points_begins =
			Yii::app()
				->db
				->createCommand(
					'SELECT SUBSTRING_INDEX(text, ",", 1) text FROM {{points}}'
				)
				->queryAll();
		$points_begins = array_map(
			function($point_begin) {
				$point_begin = $point_begin['text'];
				$point_begin_length = strlen($point_begin);
				if (
					$point_begin_length != 0
					and $point_begin[$point_begin_length - 1] == ';'
				) {
					$point_begin = substr(
						$point_begin,
						0,
						$point_begin_length - 1
					);
				}

				return $point_begin;
			},
			$points_begins
		);
		$points_begins = array_unique($points_begins);

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'points_begins' => $points_begins
			)
		);
	}

	public function actionCreate() {
		if (isset($_POST['Point'])) {
			$model = new Point();
			$model->attributes = $_POST['Point'];
			$result = $model->save();

			if ($result) {
				$date = date('Y-m-d');
				Point::renumberOrderFieldsForDate($date);
			}
		}
	}

	public function actionUpdate($id) {
		if (isset($_POST['Point'])) {
			$model = $this->loadModel($id);
			$model->attributes = $_POST['Point'];
			$result = $model->save();

			if ($result) {
				if (isset($_POST['Point']['text'])) {
					echo $model->text;
				}
				if (isset($_POST['Point']['order'])) {
					Point::renumberOrderFieldsForDate($model->date);
				}
			}
		}
	}

	public function actionDelete($id) {
		$model = $this->loadModel($id);
		$date = $model->date;
		$model->delete();

		Point::renumberOrderFieldsForDate($date);
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
