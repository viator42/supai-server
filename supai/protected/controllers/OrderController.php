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
			$order['readed'] = $orderObj->readed;
			
			$data[] = $order;

		}

		$result['success'] = true;
		$result['data'] = $data;

		$json = CJSON::encode($result);
        echo $json;
	}

	//查询订单,包括商户/客户的
	public function actionActiveOrders()
	{
		$result = array();
		$data = array();

		$userid = $_POST['userid'];
		$type = $_POST['type'];		//查询类型 1:已提交 配送中 2:已完成 已取消
		$customerPage = $_POST['cpage'];	//客户列表当前页数
		$merchantPage = $_POST['mpage'];	//商户列表当前页数

		$limit =  (int)$_POST['limit'];	//每页的个数

		$forCustomer = array();
		$forMerchant = array();

		//客户列表
		switch ($type) {
			case 1:
				$orderObjs = Order::model()->findAll('(status = 1 or status = 2) and customer_id=:customer_id limit :offset, :limit', array(':customer_id'=>$userid, ':offset'=>($customerPage * $limit), ':limit'=>$limit));
				break;
			
			case 2:
				$orderObjs = Order::model()->findAll('(status = 3 or status = 4) and customer_id=:customer_id limit :offset, :limit', array(':customer_id'=>$userid, ':offset'=>($merchantPage * $limit), ':limit'=>$limit));
				break;
		}
		foreach ($orderObjs as $orderObj) 
		{
            $order = $this->getOrderInfo($orderObj);
            if($order != null)
            {
                $forCustomer[] = $order;
            }

		}

		//商户列表
		switch ($type) {
			case 1:
				$orderObjs = Order::model()->findAll('(status = 1 or status = 2) and merchant_id=:merchant_id limit :offset, :limit', array(':merchant_id'=>$userid, ':offset'=>($merchantPage * $limit), ':limit'=>$limit));
				break;
			
			case 2:
				$orderObjs = Order::model()->findAll('(status = 3 or status = 4) and merchant_id=:merchant_id limit :offset, :limit', array(':merchant_id'=>$userid, ':offset'=>($merchantPage * $limit), ':limit'=>$limit));
				break;
		}

		foreach ($orderObjs as $orderObj) 
		{
            $order = $this->getOrderInfo($orderObj);
            if($order != null)
            {
                $forMerchant[] = $order;
            }
		}

		$result['merchantList'] = $forMerchant;
		$result['customerList'] = $forCustomer;

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//查询订单详情 商品列表
	public function actionDetail()
	{
        $result = array('success'=>false);

		$orderId = $_POST['orderId'];

		//查询订单信息
		$orderObj = Order::model()->findByPk($orderId);
        $orderResult = $this->getOrderInfo($orderObj);

        if($orderResult != null)
        {
            $result = $orderResult;

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
                    $detail['image'] = 'http://'.$_SERVER['SERVER_NAME'].$img->url;
                    $detail['name'] = $product->alias;
                    if($goods != null)
                    {
                        $detail['goodsDescription'] = $goods->description;
                        $detail['rccode'] = $goods->barcode;
                        $detail['origin'] = $goods->origin;
                    }
                    else
                    {
                        $detail['goodsDescription'] = $product->description;
                        $detail['rccode'] = '';
                        $detail['origin'] = '';

                    }

                }

                $orderDetailList[] = $detail;

            }
            $result['orderDetailList'] = $orderDetailList;

            $result['success'] = true;
        }
        else
        {
            $result['success'] = false;

        }

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

            $merchant = User::model()->findByPk($orderObj->merchant_id);
            $customer = User::model()->findByPk($orderObj->customer_id);
            if($merchant != null && $customer != null)
            {
                //发送推送通知
                $extras = array("order_id"=>$orderObj->id);
                $extras['type'] = "CANCEL_ORDER";
                $result['msg'] = sendMsg(array($merchant->sn, $customer->sn), "您好,编号 ".$orderObj->sn." 的订单已被取消.", $extras);

            }

			$result['success'] = true;

		}

		//发送推送通知

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

			$merchant = User::model()->findByPk($orderObj->merchant_id);
			if($merchant != null)
			{
				//发送推送通知 给商家
                $extras = array("order_id"=>$orderObj->id);
                $extras['type'] = "DELIVERED_ORDER";
				$result['msg'] = $this->sendMsg(array($merchant->sn), "您好,编号 ".$orderObj->sn." 的订单已确认收货.", $extras);

			}

            //收藏购买的店铺和订单的商品.
            $storeCollectObj = StoreCollect::model()->find('user_id=:user_id and store_id=:store_id',
                array(':user_id'=>$orderObj->customer_id, ':store_id'=>$orderObj->store_id));
            if($storeCollectObj == null)
            {
                $storeCollectObj = new StoreCollect();
                $storeCollectObj->user_id = $orderObj->customer_id;
                $storeCollectObj->store_id = $orderObj->store_id;

                $storeCollectObj->save();
            }

            //遍历收藏购买订单的商品
            $orderDetailObjs = OrderDetail::model()->findAll('order_id=:order_id', array(':order_id'=>$orderId));
            foreach ($orderDetailObjs as $orderDetailObj)
            {
                $productCollect = new ProductCollect();

                $productCollect->product_id = $orderDetailObj->product_id;
                $productCollect->store_collect_id = $storeCollectObj->id;
                $productCollect->user_id = $orderObj->customer_id;

                $productCollect->save();
            }

            //用户添加到店铺的follower列表
            $followerObj = Follower::model()->find('customer_id=:customer_id, store_id=:store_id',
                array(':customer_id'=>$orderObj->customer_id, ':store_id'=>$orderObj->store_id));
            if($followerObj == null)
            {
                $followerObj = new Follower();

                $followerObj->customer_id = $orderObj->customer_id;
                $followerObj->store_id = $orderObj->store_id;

                $followerObj->save();
            }

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
			$orderObj->readed = 1;
			$orderObj->save();

			$customer = User::model()->findByPk($orderObj->customer_id);
			if($customer != null)
			{
				//发送推送通知 给客户
                $extras = array("order_id"=>$orderObj->id);
                $extras['type'] = "CONFIRMED_ORDER";

				$result['msg'] = sendMsg(array($customer->sn), "您好,编号 ".$orderObj->sn." 的订单已发货.", $extras);

			}

			$result['success'] = true;
		}
		
		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//

	//购物车生成订单
	

	//返回用户的历史订单
	public function actionHistory()
	{
		$result = array();
		$userid = $_POST['userid'];

		$orderObjs = Order::model()->findAll('(status = 3 or status = 4) and customer_id=:customer_id', array(':customer_id'=>$userid));
		
		
		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//订单设置为已读
	public function actionReaded()
	{
		$result = array('success'=>false);

		$orderId = $_POST['orderId'];

		$orderObj = Order::model()->findByPk($orderId);
		if($orderObj != null)
		{
			$orderObj->readed = 2;
			$orderObj->save();
			$result['success'] = true;

		}
		
		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;

	}

    //获取订单信息
    private function getOrderInfo($orderObj)
    {
        $order = array();

        $customer = User::model()->findByPk($orderObj->customer_id);
        $merchant = User::model()->findByPk($orderObj->merchant_id);
        $store = Store::model()->findByPk($orderObj->store_id);
        if($customer != null && $merchant != null && $store != null)
        {
            //订单信息
            $order['id'] = $orderObj->id;
            $order['sn'] = $orderObj->sn;
            $order['merchantId'] = $orderObj->merchant_id;
            $order['customerId'] = $orderObj->customer_id;
            $order['createTime'] = $orderObj->create_time;
            $order['summary'] = $orderObj->summary;
            $order['status'] = $orderObj->status;
            $order['additional'] = $orderObj->additional;
            $order['readed'] = $orderObj->readed;
            //商店信息
            $order['storeId'] = $orderObj->store_id;
            $order['storeName'] = $store->name;
            $order['storeAddress'] = $orderObj->address;
            //用户信息
            $order['customerName'] = $customer->name;
            $order['merchantName'] = $merchant->name;
            $order['customerTel'] = $customer->tel;
            $order['merchantTel'] = $merchant->tel;
            $order['customerAddress'] = $customer->address;
            $order['merchantAddress'] = $merchant->address;
            $order['customerLongitude'] = $customer->longitude;
            $order['customerLatitude'] = $customer->latitude;
            $order['merchantLongitude'] = $merchant->longitude;
            $order['merchantLatitude'] = $merchant->latitude;

            return $order;
        }
        else
        {
            return null;
        }

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