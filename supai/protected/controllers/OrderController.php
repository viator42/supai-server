<?php

//订单
class OrderController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'users'=>array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
//                'actions'=>array('*'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
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

	//返回用户已提交,已发货的所有订单
	public function actionActiveOrdersForCustomer()
	{
		$result = array();
		$data = array();

		$customerId = $_POST['id'];
		//$orderObjs = Order::model()->findAll('customer_id = :customerId and (status = 1 or status = 2)', array(':customerId'=>$customerId));
		
		$orderObjs = Order::model()->findAll('status = 1 or status = 2');
		foreach ($orderObjs as $orderObj) 
		{
			$order = array();

			$order['id'] = $orderObj->id;
			$order['merchantId'] = $orderObj->merchant_id;
			$order['storeId'] = $orderObj->store_id;

			//查询商家信息
			$order['merchantName'] = "";
			$merchant = User::model()->findByPk($orderObj->merchant_id);
			if($merchant != null)
			{
				$order['merchantName'] = $merchant->name;
			}

			//查询商店信息
			$order['storeName'] = "";
			$store = Store::model()->findByPk($orderObj->store_id);
			if($store != null)
			{
				$order['storeName'] = $store->name;
			}

			$order['createTime'] = $orderObj->create_time;
			$order['summary'] = $orderObj->summary;
			$order['status'] = $orderObj->getStatusName();
			$order['additional'] = $orderObj->additional;
			
			$data[] = $order;

		}

		$result['success'] = true;
		$result['data'] = $data;

		$json = CJSON::encode($result);
        echo $json;
	}

	//查询订单详情 商品列表
	public function actionDetail()
	{
		$result = array();
		$data = array();

		$orderId = $_POST['id'];

		//查询订单信息
		$orderObj = Order::model()->findByPk($orderId);
		$data['create_time'] = $orderObj->create_time;

		$data['store_id'] = $orderObj->store_id;
		$store = Store::model()->findByPk($orderObj->store_id);
		$data['store_name'] = $store->name;

		$data['status'] = $orderObj->getStatusName();

		//商品列表
		$detailList = array();

		$orderDetailObjs = OrderDetail::model()->findAll('order_id=:order_id', array(':order_id'=>$orderId));
		foreach ($orderDetailObjs as $orderObj) 
		{
			$detail = array();
			$detail['id'] = $orderObj->id;
			$detail['productId'] = $orderObj->product_id;
			$detail['count'] = $orderObj->count;
			$detail['price'] = $orderObj->price;

			//获取订单产品信息
			$product = Product::model()->findByPk($orderObj->product_id);
			$goods = Goods::model()->findByPk($product->goods_id);
			
			if($product != null)
			{
				//产品图片
				$detail['image'] = "http://192.168.1.10/images/aaaa.jpg";

				$detail['name'] = $goods->name;
				$detail['goodsDescription'] = $goods->description;
				$detail['rccode'] = $goods->rccode;
				$detail['origin'] = $goods->origin;

			}

			$detailList[] = $detail;

		}
		$result['detailList'] = $detailList;

		$result['success'] = true;
		$result['data'] = $data;

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//返回用户的历史订单


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