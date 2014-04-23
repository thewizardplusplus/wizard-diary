<?php

class SiteController extends CController {
	public function filters() {
		return array('accessControl', 'postOnly + logout');
	}

	public function accessRules() {
		return array(
			array('allow', 'actions' => array('error', 'login')),
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionError() {
		if (!is_null(Yii::app()->errorHandler->error)) {
			$this->render('error', Yii::app()->errorHandler->error);
		} else {
			$this->redirect(Yii::app()->homeUrl);
		}
	}

	public function actionLogin() {
		if (!Yii::app()->user->isGuest) {
			$this->redirect(Yii::app()->homeUrl);
		}

		$model = new LoginForm();

		if (isset($_POST['ajax']) and $_POST['ajax'] == 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (
			isset($_POST['LoginForm'])
			and isset($_POST['codecha_challenge_field'])
			and isset($_POST['codecha_response_field'])
		) {
			$model->attributes = $_POST['LoginForm'];
			$result = $model->validate();
			if ($result) {
				$result = Codecha::check(
					$_POST['codecha_challenge_field'],
					$_POST['codecha_response_field'],
					$_SERVER['REMOTE_ADDR']
				);
				if ($result) {
					$result = $model->login();
					if ($result) {
						$this->redirect(Yii::app()->user->returnUrl);
					}
				} else {
					$model->addError(
						'verify_code',
						'Тест Тьюринга не пройден.'
					);
				}
			}
		}

		$password_container_class =
			count($model->getErrors('password'))
				? 'has-error'
				: '';
		$verify_code_container_class =
			count($model->getErrors('verify_code'))
				? 'has-error'
				: '';
		$this->render(
			'login',
			array(
				'model' => $model,
				'password_container_class' => $password_container_class,
				'verify_code_container_class' => $verify_code_container_class
			)
		);
	}

	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}
