<?php

require_once(__DIR__ . '/../../recaptcha/recaptchalib.php');

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
					if (Parameters::getModel()->use_2fa) {
						AccessCode::send($model->need_remember);
						$this->redirect($this->createUrl('site/accessCode'));
					} else {
						$this->login($model->need_remember);
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

	public function actionAccessCode() {
		if (!Yii::app()->user->isGuest) {
			$this->redirect(Yii::app()->homeUrl);
		} else if (!AccessCode::isSetted()) {
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
				$this->login(AccessCode::isNeedUserRemember(), function() {
					AccessCode::clean();
				});
			}
		}

		$access_code_lifetime = AccessCode::getRemainingLifetime();
		$access_code_container_class =
			count($model->getErrors('access_code'))
				? 'has-error'
				: '';
		$this->render(
			'access_code',
			array(
				'model' => $model,
				'access_code_lifetime' => $access_code_lifetime,
				'access_code_container_class' => $access_code_container_class
			)
		);
	}

	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	private function login($is_need_remember, $prehandler = null) {
		$result = Yii::app()->user->login(
			new DummyUserIdentity(),
			$is_need_remember
				? Parameters::getModel()->session_lifetime_in_min * 60
				: 0
		);
		if ($result) {
			if (!is_null($prehandler)) {
				$prehandler();
			}

			$user_info = new UserInfo;
			$user_info->save();

			$this->redirect(Yii::app()->user->returnUrl);
		}
	}
}
