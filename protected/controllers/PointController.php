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

		/*if (!isset($_GET['ajax']) or $_GET['ajax'] != 'point_list') {
			$pagination = $data_provider->pagination;
			$pagination->setItemCount($data_provider->getTotalItemCount());
			$pagination->currentPage = $pagination->pageCount - 1;
		}*/

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionCreate() {
		if (isset($_POST['Point'])) {
			$model = new Point;
			$model->attributes = $_POST['Point'];
			$result = $model->save();

			if ($result) {
				Point::renumberOrderFieldsForDate($model->date);
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
		$this->loadModel($id)->delete();
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
