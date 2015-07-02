<?php

/**
 * This is the model class for table "goods".
 *
 * The followings are the available columns in table 'goods':
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $barcode
 * @property string $price_interval
 * @property string $description
 * @property string $origin
 * @property string $merchant_code
 * @property string $merchant
 * @property string $unit
 * @property string $spec
 */
class Goods extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'goods';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, barcode', 'required'),
			array('category_id', 'numerical', 'integerOnly'=>true),
			array('name, spec', 'length', 'max'=>45),
			array('barcode', 'length', 'max'=>20),
			array('price_interval', 'length', 'max'=>32),
			array('origin, merchant_code, merchant', 'length', 'max'=>64),
			array('unit', 'length', 'max'=>12),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, category_id, name, barcode, price_interval, description, origin, merchant_code, merchant, unit, spec', 'safe', 'on'=>'search'),
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
			'category_id' => 'Category',
			'name' => 'Name',
			'barcode' => 'Barcode',
			'price_interval' => 'Price Interval',
			'description' => 'Description',
			'origin' => 'Origin',
			'merchant_code' => 'Merchant Code',
			'merchant' => 'Merchant',
			'unit' => 'Unit',
			'spec' => 'Spec',
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
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('barcode',$this->barcode,true);
		$criteria->compare('price_interval',$this->price_interval,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('origin',$this->origin,true);
		$criteria->compare('merchant_code',$this->merchant_code,true);
		$criteria->compare('merchant',$this->merchant,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('spec',$this->spec,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Goods the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
