<?php

class ParametersController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
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

		$password_container_class =
			count($model->getErrors('password'))
				? ' has-error'
				: '';
		$password_copy_container_class =
			count($model->getErrors('password_copy'))
				? ' has-error'
				: '';
		$session_lifetime_container_class =
			count($model->getErrors('session_lifetime_in_min'))
				? ' has-error'
				: '';
		$access_log_lifetime_container_class =
			count($model->getErrors('access_log_lifetime_in_s'))
				? ' has-error'
				: '';
		$this->render(
			'update',
			array(
				'model' => $model,
				'password_container_class' => $password_container_class,
				'password_copy_container_class' =>
					$password_copy_container_class,
				'session_lifetime_container_class' =>
					$session_lifetime_container_class,
				'access_log_lifetime_container_class' =>
					$access_log_lifetime_container_class
			)
		);
	}
}
