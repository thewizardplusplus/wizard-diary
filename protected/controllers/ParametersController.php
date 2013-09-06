<?php

class ParametersController extends Controller {
	public function __construct($id, $module = NULL) {
		parent::__construct($id, $module);
		$this->defaultAction = 'update';
	}

	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array(
				'allow',
				'actions' => array('update'),
				'users' => array('admin')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionUpdate() {
		$model = new ParametersForm;

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'parameters-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['ParametersForm'])) {
			$model->attributes = $_POST['ParametersForm'];
			if ($model->validate()) {
				$model->getParameters()->save();
			}
		}

		$this->render('update', array('model' => $model));
	}
}
