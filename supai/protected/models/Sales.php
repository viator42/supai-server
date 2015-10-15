<?php

/**
 * This is the model class for table "sales".
 *
 * The followings are the available columns in table 'sales':
 * @property integer $id
 * @property integer $product_id
 * @property integer $count
 * @property integer $status
 * @property string $price
 * @property integer $clerk_id
 * @property integer $add_time
 * @property integer $store_id
 */
class Sales extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sales';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, store_id', 'required'),
			array('product_id, count, status, clerk_id, add_time, store_id', 'numerical', 'integerOnly'=>true),
			array('price', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, product_id, count, status, price, clerk_id, add_time, store_id', 'safe', 'on'=>'search'),
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
			'product_id' => 'Product',
			'count' => 'Count',
			'status' => 'Status',
			'price' => 'Price',
			'clerk_id' => 'Clerk',
			'add_time' => 'Add Time',
			'store_id' => 'Store',
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
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('count',$this->count);
		$criteria->compare('status',$this->status);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('clerk_id',$this->clerk_id);
		$criteria->compare('add_time',$this->add_time);
		$criteria->compare('store_id',$this->store_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Sales the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
