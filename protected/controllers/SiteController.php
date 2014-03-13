<?php

class SiteController extends CController {
	public function filters() {
		return array('accessControl', 'postOnly + logout');
	}

	public function accessRules() {
		return array(
			array('allow', 'actions' => array('captcha', 'error', 'login')),
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actions(){
		return array(
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'foreColor' => 0xffffff,
				'backColor' => 0x428bca,
				'testLimit' => 1
			)
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
			if ($result) {
				$result = $model->login();
				if ($result) {
					$this->redirect(Yii::app()->user->returnUrl);
				}
			}
		}

		$password_container_class =
			count($model->getErrors('password'))
				? 'has-error'
				: '';
		$this->render(
			'login',
			array(
				'model' => $model,
				'password_container_class' => $password_container_class
			)
		);
	}

	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}
