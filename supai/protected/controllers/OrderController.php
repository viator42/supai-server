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

	//查询当前订单,包括商户/客户的
	public function actionActiveOrders()
	{
		$result = array();
		$data = array();

		$userid = $_POST['userid'];

		$forCustomer = array();
		$forMerchant = array();

		//客户列表
		$orderObjs = Order::model()->findAll('(status = 1 or status = 2) and customer_id=:customer_id', array(':customer_id'=>$userid));
		foreach ($orderObjs as $orderObj) 
		{
			$order = array();

			$order['id'] = $orderObj->id;
			$order['merchantId'] = $orderObj->merchant_id;
			$order['storeId'] = $orderObj->store_id;

			//查询商家信息
			$order['name'] = "";
			$user = User::model()->findByPk($orderObj->merchant_id);
			if($user != null)
			{
				$order['name'] = $user->name;
				$order['tel'] = $user->tel;
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
			$order['status'] = $orderObj->status;
			$order['additional'] = $orderObj->additional;
			
			$forCustomer[] = $order;

		}

		//商户列表
		$orderObjs = Order::model()->findAll('(status = 1 or status = 2) and merchant_id=:merchant_id', array(':merchant_id'=>$userid));
		foreach ($orderObjs as $orderObj) 
		{
			$order = array();

			$order['id'] = $orderObj->id;
			$order['merchantId'] = $orderObj->merchant_id;
			$order['storeId'] = $orderObj->store_id;

			//查询客户信息
			$order['name'] = "";
			$user = User::model()->findByPk($orderObj->customer_id);
			if($user != null)
			{
				$order['name'] = $user->name;
				$order['tel'] = $user->tel;
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
			$order['status'] = $orderObj->status;
			$order['additional'] = $orderObj->additional;
			
			$forMerchant[] = $order;

		}

		$result['merchantList'] = $forMerchant;
		$result['customerList'] = $forCustomer;

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//查询订单详情 商品列表
	public function actionDetail()
	{
		$result = array();

		$orderId = $_POST['orderId'];

		//查询订单信息
		$orderObj = Order::model()->findByPk($orderId);
		$result['id'] = $orderObj->id;
		$result['create_time'] = $orderObj->create_time;
		$result['store_id'] = $orderObj->store_id;
		$store = Store::model()->findByPk($orderObj->store_id);
		$result['store_name'] = $store->name;

		$result['status'] = $orderObj->status;

		//商品列表
		$orderDetailList = array();

		$orderDetailObjs = OrderDetail::model()->findAll('order_id=:order_id', array(':order_id'=>$orderId));
		foreach ($orderDetailObjs as $orderDetailObj) 
		{
			$detail = array();
			$detail['id'] = $orderDetailObj->id;
			$detail['productId'] = $orderDetailObj->product_id;
			$detail['count'] = $orderDetailObj->count;
			$detail['price'] = $orderDetailObj->price;

			//获取订单产品信息
			$product = Product::model()->findByPk($orderDetailObj->product_id);
			$goods = Goods::model()->findByPk($product->goods_id);
			
			if($product != null)
			{
				//产品图片
				$img = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$product->id));
				$detail['image'] = $img->url;
				$detail['name'] = $goods->name;
				$detail['goodsDescription'] = $goods->description;
				$detail['rccode'] = $goods->barcode;
				$detail['origin'] = $goods->origin;

			}

			$orderDetailList[] = $detail;

		}
		$result['orderDetailList'] = $orderDetailList;

		$result['success'] = true;

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//取消订单
	public function actionCancel()
	{
		$result = array('success'=>false);

		$orderId = $_POST['orderId'];

		$orderObj = Order::model()->findByPk($orderId);
		if($orderObj != null)
		{
			$orderObj->status = 5;
			$orderObj->save();
			$result['success'] = true;

		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//确认收货
	public function actionDelivered()
	{
		$result = array('success'=>false);

		$orderId = $_POST['orderId'];

		$orderObj = Order::model()->findByPk($orderId);
		if($orderObj != null)
		{
			$orderObj->status = 4;
			$orderObj->save();
			$result['success'] = true;

		}
		
		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//商家确认订单,开始发货
	public function actionConfirm()
	{
		$result = array('success'=>false);

		$orderId = $_POST['orderId'];

		$orderObj = Order::model()->findByPk($orderId);
		if($orderObj != null)
		{
			$orderObj->status = 2;
			$orderObj->save();
			$result['success'] = true;

		}
		
		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//

	//购物车生成订单
	

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