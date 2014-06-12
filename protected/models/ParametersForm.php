<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $points_on_page;
	public $dropbox_access_token;

	public function __construct() {
		parent::__construct();

		$this->points_on_page = Parameters::getModel()->points_on_page;
		$this->dropbox_access_token =
			Parameters::getModel()->dropbox_access_token;
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
				'default',
				'value' => Constants::POINTS_ON_PAGE_DEFAULT
			),
			array(
				'points_on_page',
				'numerical',
				'min' => Parameters::POINTS_ON_PAGE_MINIMUM,
				'max' => Parameters::POINTS_ON_PAGE_MAXIMUM,
				'message' => '{attribute} должно быть числом.',
				'tooSmall' => '{attribute} должно быть не меньше {min}.',
				'tooBig' => '{attribute} должно быть не больше {max}.',
			),
			array('dropbox_access_token', 'required'),
			array(
				'dropbox_access_token',
				'length',
				'max' => Parameters::DROPBOX_ACCESS_TOKEN_LENGTH_MAXIMUM,
				'tooLong' =>
					'{attribute} должен быть не длиннее {max} символов.'
			)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль',
			'password_copy' => 'Пароль (копия)',
			'points_on_page' => 'Число пунктов на странице',
			'dropbox_access_token' => 'Токен доступа к Dropbox'
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
		$model->dropbox_access_token = $this->dropbox_access_token;
		$model->save();
	}
}
