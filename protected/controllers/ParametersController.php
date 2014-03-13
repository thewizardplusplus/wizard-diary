<?php

class ParametersController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionUpdate() {
		$model = new ParametersForm();

		if (isset($_POST['ajax']) and $_POST['ajax'] == 'parameters-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['ParametersForm'])) {
			$model->attributes = $_POST['ParametersForm'];
			$result = $model->validate();
			if ($result) {
				$model->save();

				$model->password = '';
				$model->password_copy = '';
			}
		}

		$start_date_container_class =
			count($model->getErrors('start_date'))
				? ' has-error'
				: '';
		$password_copy_container_class =
			count($model->getErrors('password_copy'))
				? ' has-error'
				: '';
		$this->render(
			'update',
			array(
				'model' => $model,
				'start_date_container_class' => $start_date_container_class,
				'password_copy_container_class' =>
					$password_copy_container_class
			)
		);
	}
}
