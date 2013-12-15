<?php

class PointController extends CController {
	public function __construct($id, $module = NULL) {
		parent::__construct($id, $module);
		$this->defaultAction = 'list';
	}

	public function filters() {
		return array(
			'accessControl',
			'postOnly + delete'
		);
	}

	public function accessRules() {
		return array(
			array(
				'allow',
				'actions' => array('list', 'create', 'update', 'delete'),
				'users' => array('admin')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider('Point', array(
			'criteria' => array('order' => 'date, `order`'),
			'pagination' => array('pagesize' => Parameters::get()->
				points_on_page)
		));

		if (!isset($_GET['ajax']) or $_GET['ajax'] != 'point_list') {
			$pagination = $data_provider->pagination;
			$pagination->setItemCount($data_provider->getTotalItemCount());
			$pagination->currentPage = $pagination->pageCount - 1;
		}

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionCreate() {
		if (isset($_POST['Point'])) {
			$model = new Point;
			$model->attributes = $_POST['Point'];
			$model->save();
		}

		if (!isset($_POST['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl']
				: array('list'));
		}
	}

	public function actionUpdate($id) {
		if (isset($_POST['Point'])) {
			$model = $this->loadModel($id);
			$model->attributes = $_POST['Point'];
			$result = $model->save();

			if (isset($_POST['Point']['text']) and $result) {
				echo $model->text;
				return;
			}
		}

		if (!isset($_POST['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl']
				: array('list'));
		}
	}

	public function actionDelete($id) {
		$this->loadModel($id)->delete();

		if (!isset($_POST['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] :
				array('list'));
		}
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
