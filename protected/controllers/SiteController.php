<?php

require_once('recaptcha/recaptchalib.php');
require_once('sms-ru/src/smsru.php');

class SiteController extends CController {
	public function filters() {
		return array('accessControl', 'postOnly + logout');
	}

	public function accessRules() {
		return array(
			array('allow', 'actions' => array('error', 'login', 'accessCode')),
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
					$this->sendAccessCode();
					$this->redirect($this->createUrl('site/accessCode'));
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

	public function actionAccessCode() {
		if (!Yii::app()->user->isGuest) {
			$this->redirect(Yii::app()->homeUrl);
		} else if (is_null(Yii::app()->session['ACCESS_CODE'])) {
			$this->redirect($this->createUrl('site/login'));
		}

		$model = new AccessCodeForm();

		if (isset($_POST['ajax']) and $_POST['ajax'] == 'access-code-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['AccessCodeForm'])) {
			$model->attributes = $_POST['AccessCodeForm'];
			$result = $model->validate();
			if ($result) {
				$result = Yii::app()->user->login(new DummyUserIdentity());
				if ($result) {
					unset(Yii::app()->session['ACCESS_CODE']);
					$this->redirect(Yii::app()->user->returnUrl);
				}
			}
		}

		$access_code_container_class =
			count($model->getErrors('access_code'))
				? 'has-error'
				: '';
		$this->render(
			'access_code',
			array(
				'model' => $model,
				'access_code_container_class' => $access_code_container_class
			)
		);
	}

	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	private function sendAccessCode() {
		$sms_sender = new \Zelenin\smsru(
			null,
			Constants::SMS_RU_LOGIN,
			Constants::SMS_RU_PASSWORD
		);
		$result = $sms_sender->auth_check();
		if ($result['code'] != 100) {
			throw new CException(
				'Ошибка отправки кода доступа. ' . $result['description']
			);
		}

		$access_code = $this->getAccessCode();
		$result = $sms_sender->sms_send(Constants::SMS_RU_LOGIN, $access_code);
		if ($result['code'] != 100) {
			throw new CException(
				'Ошибка отправки кода доступа. ' . $result['description']
			);
		}

		Yii::app()->session['ACCESS_CODE'] = $access_code;
	}

	private function getAccessCode() {
		$access_code = '';
		for ($i = 0; $i < Constants::ACCESS_CODE_LENGTH; $i++) {
			$access_code .= rand(0, 9);
		}

		return $access_code;
	}
}
