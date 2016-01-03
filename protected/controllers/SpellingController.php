<?php

class SpellingController extends CController {
	public function filters() {
		return array(
			'accessControl',
			'postOnly + add, delete, deleteAll',
			'ajaxOnly + add, delete, deleteAll'
		);
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider(
			'Spelling',
			array('criteria' => array('order' => 'word'))
		);

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionAdd() {
		if (!isset($_POST['word'])) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}

		$spelling = new Spelling;
		$spelling->word = $_POST['word'];
		$result = $spelling->save();
		if (!$result) {
			throw new CException('Данное слово нельзя добавить в словарь.');
		}
	}

	public function actionDelete() {
		if (!isset($_POST['id']) or !is_numeric($_POST['id'])) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}

		Spelling::model()->deleteByPk($_POST['id']);
	}

	public function actionDeleteAll() {
		Spelling::model()->deleteAll();
	}
}
