<?php

class DeleteByQueryForm extends CFormModel {
	public $query;

	public function rules() {
		return array(array('query', 'required'));
	}

	public function attributeLabels() {
		return array('query' => 'Запрос');
	}
}
