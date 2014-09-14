<?php

require_once('recaptcha/recaptchalib.php');

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

		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			$result = $model->validate();
			if (
				$result
				and isset($_POST['recaptcha_challenge_field'])
				and isset($_POST['recaptcha_response_field'])
			) {
				$result = recaptcha_check_answer(
					Constants::RECAPTCHA_PRIVATE_KEY,
					$_SERVER['REMOTE_ADDR'],
					$_POST['recaptcha_challenge_field'],
					$_POST['recaptcha_response_field']
				);
				if ($result->is_valid) {
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
