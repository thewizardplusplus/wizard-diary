<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $points_on_page;

	public function __construct() {
		parent::__construct();
		$this->points_on_page = Parameters::getModel()->points_on_page;
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array(
				'password_copy',
				'compare',
				'compareAttribute' => 'password',
				'message' =>
					'Поле &laquo;{attribute}&raquo; должно в точности '
						. 'повторять поле &laquo;{compareAttribute}&raquo;.'
			),
			array(
				'points_on_page',
				'numerical',
				'min' => Parameters::POINTS_ON_PAGE_MINIMUM,
				'max' => Parameters::POINTS_ON_PAGE_MAXIMUM,
				'skipOnError' => true,
				'message' => '{attribute} должно быть числом.',
				'tooSmall' => '{attribute} должно быть не меньше {min}.',
				'tooBig' => '{attribute} должно быть не больше {max}.',
			)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль',
			'password_copy' => 'Пароль (копия)',
			'points_on_page' => 'Число пунктов на странице'
		);
	}

	public function save() {
		$model = Parameters::getModel();
		if (!empty($this->password)) {
			$model->password_hash = CPasswordHelper::hashPassword(
				$this->password
			);
		}
		$model->points_on_page = $this->points_on_page;
		$model->save();
	}
}
