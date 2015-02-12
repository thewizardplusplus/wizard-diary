<?php

class ImportController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider(
			'Import',
			array('criteria' => array('order' => 'date DESC'))
		);

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionView($id) {
		$model = $this->loadModel($id);
		$this->render('view', array('model' => $model));
	}

	public function actionCreate() {
		$model = new Import;
		$this->performAjaxValidation($model);

		if (isset($_POST['Import'])) {
			$model->attributes = $_POST['Import'];
			$result = $model->save();
			if ($result) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array('model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id);
		$this->performAjaxValidation($model);

		if (isset($_POST['Import'])) {
			$model->attributes = $_POST['Import'];
			$result = $model->save();
			if ($result) {
				$this->redirect(array('view', 'id' => $id));
			}
		}

		$this->render('update', array('model' => $model));
	}

	public function actionDelete($id) {
		$this->loadModel($id)->delete();
		$this->redirect($_POST['returnUrl']);
	}

	private function loadModel($id) {
		$model = Import::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}

	private function performAjaxValidation($model) {
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'import-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
