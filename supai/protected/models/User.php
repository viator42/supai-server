<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $imie
 * @property string $username
 * @property string $password
 * @property string $tel
 * @property string $icon
 * @property integer $register_time
 * @property integer $lastlogin_time
 * @property string $name
 * @property integer $area_id
 * @property string $address
 * @property double $longitude
 * @property double $latitude
 * @property integer $status
 * @property string $sn
 * @property string $passtype
 * @property integer $clerk_of
 * @property string $token
 * @property integer $expire_time
 * @property integer $print_copy
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('imie, username, password, tel, sn', 'required'),
			array('register_time, lastlogin_time, area_id, status, clerk_of, expire_time, print_copy', 'numerical', 'integerOnly'=>true),
			array('longitude, latitude', 'numerical'),
			array('imie, password, icon, address', 'length', 'max'=>128),
			array('username, name, passtype', 'length', 'max'=>45),
			array('tel', 'length', 'max'=>20),
			array('sn', 'length', 'max'=>64),
			array('token', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, imie, username, password, tel, icon, register_time, lastlogin_time, name, area_id, address, longitude, latitude, status, sn, passtype, clerk_of, token, expire_time, print_copy', 'safe', 'on'=>'search'),
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
			'imie' => 'Imie',
			'username' => 'Username',
			'password' => 'Password',
			'tel' => 'Tel',
			'icon' => 'Icon',
			'register_time' => 'Register Time',
			'lastlogin_time' => 'Lastlogin Time',
			'name' => 'Name',
			'area_id' => 'Area',
			'address' => 'Address',
			'longitude' => 'Longitude',
			'latitude' => 'Latitude',
			'status' => 'Status',
			'sn' => 'Sn',
			'passtype' => 'Passtype',
			'clerk_of' => 'Clerk Of',
			'token' => 'Token',
			'expire_time' => 'Expire Time',
			'print_copy' => 'Print Copy',
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
		$criteria->compare('imie',$this->imie,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('icon',$this->icon,true);
		$criteria->compare('register_time',$this->register_time);
		$criteria->compare('lastlogin_time',$this->lastlogin_time);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('area_id',$this->area_id);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('status',$this->status);
		$criteria->compare('sn',$this->sn,true);
		$criteria->compare('passtype',$this->passtype,true);
		$criteria->compare('clerk_of',$this->clerk_of);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('expire_time',$this->expire_time);
		$criteria->compare('print_copy',$this->print_copy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
