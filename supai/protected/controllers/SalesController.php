<?php

class SalesController extends Controller
{
//	public function actionIndex()
//	{
//		$this->render('index');
//	}

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

    //商品列表
    public function actionList()
    {
        $result = array();

        $clerkId = $_POST['clerkId'];
        $storeId = $_POST['storeId'];

        $saleObjs = Sales::model()->findAll('store_id = :store_id and clerk_id = :clerk_id', array(':store_id'=>$storeId, ':clerk_id'=>$clerkId));
        foreach($saleObjs as $saleObj)
        {
            $product = Product::model()->findByPk($saleObj->product_id);
            if($product != null)
            {
                $sale = array();

                $sale['id'] = $saleObj->id;
                $sale['product_id'] = $saleObj->product_id;
                $sale['alias'] = $product->alias;

                $sale['count'] = $saleObj->count;
                $sale['status'] = $saleObj->status;
                $sale['price'] = $saleObj->price;
                $sale['clerk_id'] = $saleObj->clerk_id;
                $sale['add_time'] = $saleObj->add_time;
                $sale['store_id'] = $saleObj->store_id;

                //商品图片
                $image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$product->id));
                if($image != null)
                {
                    $sale['img'] = $image->url;
                }
                else
                {
                    //加载默认图片
                    $sale['img'] = "/images/product_default.jpg";
                }

                $result[] = $sale;
            }

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //添加商品
	public function actionAdd()
	{
        $result = array('success'=>false);

        $productId = $_POST['productId'];
        $clerkId = $_POST['clerkId'];
        $storeId = $_POST['storeId'];
        $price = $_POST['price'];
        $count = $_POST['count'];

        $saleItem = new Sales();

        $saleItem->clerk_id = $clerkId;
        $saleItem->product_id = $productId;
        $saleItem->store_id = $storeId;
        $saleItem->price = $price;
        $saleItem->count = $count;
        $saleItem->add_time = time();
        $saleItem->status = 1;

        $saleItem->save();

        $result['success'] = true;

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

    //删除商品
    public function actionRemove()
    {
        $result = array('success'=>false);

        $salesId = $_POST['salesId'];

        $sales = Sales::model()->findByPk($salesId);
        if($sales != null)
        {
            $sales->delete();
            $result['success'] = true;

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //清空列表
    public function actionClearAll()
    {
        $result = array('success'=>false);

        $clerkId = $_POST['clerkId'];
        $storeId = $_POST['storeId'];

        $saleObjs = Sales::model()->findAll('store_id = :store_id and clerk_id = :clerk_id', array(':store_id'=>$storeId, ':clerk_id'=>$clerkId));
        foreach($saleObjs as $saleObj)
        {
            $saleObj->delete();

        }

        $result['success'] = true;

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //修改
    public function actionEdit()
    {
        $result = array('success'=>false);

        $salesId = $_POST['salesId'];
        $price = $_POST['price'];
        $count = $_POST['count'];

        $sales = Sales::model()->findByPk($salesId);
        if($sales != null)
        {
            $sales->price = $price;
            $sales->count = $count;

            $sales->save();
            $result['success'] = true;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //生成订单
    public function actionCreateOrder()
    {
        $result = array('success'=>false);

        $clerkId = $_POST['clerkId'];
        $storeId = $_POST['storeId'];

        $saleObjs = Sales::model()->findAll('store_id = :store_id and clerk_id = :clerk_id', array(':store_id'=>$storeId, ':clerk_id'=>$clerkId));

        if(sizeof($saleObjs) > 0)
        {
            $order = new Order();

            $order->create_time = time();
            $order->customer_id = 0;
            $order->merchant_id = $clerkId;
            $order->store_id = $storeId;
            $order->sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $order->address = '';

            $order->paid = 1;
            $order->pay_method = 3;
            $order->pay_after = 1;
            $order->type = 2;

            $order->save();

            $summary = 0;//总价
            $count = 0; //数量

            //遍历添加商品
            foreach($saleObjs as $saleObj)
            {
                $orderDetail = new OrderDetail();

                $orderDetail->order_id = $order->id;
                $orderDetail->product_id = $saleObj->product_id;
                $orderDetail->count = $saleObj->count;
                $orderDetail->price = $saleObj->price;

                $orderDetail->save();

                $summary += $orderDetail->price * $orderDetail->count;
                $count += $saleObj->count;

                $saleObj->delete();
            }

            $order->count = $count;
            $order->summary = $summary;
            $order->save();

            $result['success'] = true;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //店铺销售订单
    //clerkId 对应 merchantId
    public function actionOrders()
    {
        $result = array();

        $clerkId = $_POST['clerkId'];
        $storeId = $_POST['storeId'];
        $page = $_POST['page'];
        $limit = (int)$_POST['limit'];	//每页的个数

        if($storeId != 0)
        {
            $orderObjs = Order::model()->findAll('store_id = :store_id and type = 2 order by create_time desc limit :offset, :limit',
                array(':store_id'=>$storeId, ':offset'=>($page * $limit), ':limit'=>$limit));

        }
        else
        {
            $orderObjs = Order::model()->findAll('merchant_id=:merchant_id and store_id = :store_id and type = 2 order by create_time desc limit :offset, :limit',
                array(':merchant_id'=>$clerkId, ':store_id'=>$storeId, ':offset'=>($page * $limit), ':limit'=>$limit));

        }

        foreach($orderObjs as $orderObj)
        {
            $clerkObj = User::model()->findByPk($orderObj->merchant_id);

            if($clerkObj != null)
            {
                $order = array();

                $order['id'] = $orderObj->id;
                $order['create_time'] = $orderObj->create_time;
                $order['merchant_id'] = $orderObj->merchant_id;
                $order['merchant_name'] = $clerkObj->name;
                $order['count'] = $orderObj->count;
                $order['summary'] = $orderObj->summary;

                $result[] = $order;
            }

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
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