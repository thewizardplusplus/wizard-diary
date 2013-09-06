<?php

class PointController extends Controller {
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
				'actions' => array('list', 'update', 'delete'),
				'users' => array('admin')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionList() {
		$model = new Point;

		if (isset($_POST['Point'])) {
			$model->attributes = $_POST['Point'];
			$model->save();

			$model = new Point;
		}

		$dataProvider = new CActiveDataProvider('Point', array(
			'criteria' => array('order' => 'date DESC, `order`'),
			'pagination' => array('pagesize' => Parameters::get()->
				points_on_page)
		));
		$this->render('list', array(
			'model' => $model,
			'dataProvider' => $dataProvider
		));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id);

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'point-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['Point'])) {
			$model->attributes = $_POST['Point'];
			$result = $model->save();
			if (!isset($_POST['ajax']) and $result) {
				$this->redirect(array('list'));
			}
		}
		if (isset($_POST['ajax']) and isset($_GET['shift']) and $_GET['shift']
			== 'up' and $_GET['shift'] == 'down')
		{
			if ($_GET['shift'] == 'down') {
				if ($model->order < Point::getOrderBound('maximal', $model->
					date))
				{
					$model->order = $model->order + 1;
				}
			} else if ($_GET['shift'] == 'up') {
				if ($model->order > Point::getOrderBound('minimal', $model->
						date))
				{
					$model->order = $model->order - 1;
				}
			}
			$model->save();
		}

		if (!isset($_POST['ajax'])) {
			$this->render('update', array('model' => $model));
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
