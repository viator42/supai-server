<?php

//订单
class OrderController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}


	//商户查看已提交的订单
	public function actionActiveOrdersForMerchant()
	{
		$result = array();

		$merchantId = $_POST['id'];
		// 1 已提交 		2 已发货
		$orders = Order::model()->find('merchant_id = :merchantId and (status = 1 or status = 2)', array(':merchantId'=>$merchantId));
		foreach ($orders as $order) 
		{
			$result[] = $order;

		}

		$json = CJSON::encode($result);
        echo $json;
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}