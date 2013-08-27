<?php

/**
 * This is the model class for table "{{points}}".
 *
 * The followings are the available columns in table '{{points}}':
 * @property integer $id
 * @property string $date
 * @property string $text
 * @property string $state
 */
class Point extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{points}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('text', 'required'),
			array('state', 'in', 'range' => array('INITIAL', 'SATISFIED',
				'NOT_SATISFIED','CANCELED'))
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'text' => 'Текст:',
			'state' => 'Состояние:',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Point the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->date=date("Y-m-d");
			}
			return true;
		}
		else
			return false;
	}
}
