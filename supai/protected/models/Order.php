<?php

/**
 * This is the model class for table "order".
 *
 * The followings are the available columns in table 'order':
 * @property integer $id
 * @property integer $create_time
 * @property integer $customer_id
 * @property integer $merchant_id
 * @property integer $store_id
 * @property integer $status
 * @property string $summary
 * @property string $additional
 * @property integer $readed
 * @property string $sn
 * @property string $address
 * @property integer $pay_method
 * @property integer $paid
 * @property integer $pay_after
 * @property integer $type
 * @property integer $count
 */
class Order extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time, customer_id, merchant_id, store_id, sn', 'required'),
			array('create_time, customer_id, merchant_id, store_id, status, readed, pay_method, paid, pay_after, type, count', 'numerical', 'integerOnly'=>true),
			array('summary', 'length', 'max'=>10),
			array('sn', 'length', 'max'=>64),
			array('address', 'length', 'max'=>128),
			array('additional', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, create_time, customer_id, merchant_id, store_id, status, summary, additional, readed, sn, address, pay_method, paid, pay_after, type, count', 'safe', 'on'=>'search'),
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
			'create_time' => 'Create Time',
			'customer_id' => 'Customer',
			'merchant_id' => 'Merchant',
			'store_id' => 'Store',
			'status' => 'Status',
			'summary' => 'Summary',
			'additional' => 'Additional',
			'readed' => 'Readed',
			'sn' => 'Sn',
			'address' => 'Address',
			'pay_method' => 'Pay Method',
			'paid' => 'Paid',
			'pay_after' => 'Pay After',
			'type' => 'Type',
			'count' => 'Count',
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
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('additional',$this->additional,true);
		$criteria->compare('readed',$this->readed);
		$criteria->compare('sn',$this->sn,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('pay_method',$this->pay_method);
		$criteria->compare('paid',$this->paid);
		$criteria->compare('pay_after',$this->pay_after);
		$criteria->compare('type',$this->type);
		$criteria->compare('count',$this->count);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Order the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
