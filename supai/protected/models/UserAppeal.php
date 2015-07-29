<?php

/**
 * This is the model class for table "user_appeal".
 *
 * The followings are the available columns in table 'user_appeal':
 * @property integer $id
 * @property string $old_tel
 * @property string $new_tel
 * @property string $name
 * @property string $address
 * @property string $imie
 * @property integer $area_id
 * @property integer $type
 * @property integer $create_time
 */
class UserAppeal extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_appeal';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('old_tel, new_tel, name', 'required'),
			array('area_id, type, create_time', 'numerical', 'integerOnly'=>true),
			array('old_tel, new_tel, name', 'length', 'max'=>20),
			array('address', 'length', 'max'=>45),
			array('imie', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, old_tel, new_tel, name, address, imie, area_id, type, create_time', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'old_tel' => 'Old Tel',
			'new_tel' => 'New Tel',
			'name' => 'Name',
			'address' => 'Address',
			'imie' => 'Imie',
			'area_id' => 'Area',
			'type' => 'Type',
			'create_time' => 'Create Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('old_tel',$this->old_tel,true);
		$criteria->compare('new_tel',$this->new_tel,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('imie',$this->imie,true);
		$criteria->compare('area_id',$this->area_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserAppeal the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
