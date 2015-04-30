<?php

class CartController extends Controller
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

    //购物车商品列表
    public function actionList()
    {
    	$result = array('success'=>false);
    	$data = array();

    	$userId = $_POST['userid'];

    	$cartObjs = Cart::model()->findAll('user_id=:user_id', array(':user_id'=>$userId));
    	
    	$carts = array();
    	//购物车商品
    	foreach ($cartObjs as $cartObj)
    	{
    		$summary = 0;
			$count = 0;

    		$cart = array();
    		$details = array();

            $cart['id'] = $cartObj->id;
            $cart['userId'] = $cartObj->user_id;
            $cart['status'] = $cartObj->status;

            $store = Store::model()->findByPk($cartObj->store_id);
            $cart['storeName'] = $store->name;

            $cart['storeId'] = $cartObj->store_id;

    		$detailObjs = CartDetail::model()->findAll('cart_id=:cart_id', array(':cart_id'=>$cartObj->id));
    		foreach ($detailObjs as $detailObj)
    		{
    			$detail = array();
    			$detail['id'] = $detailObj->id;
    			$detail['name'] = $detailObj->goods_name;
    			$detail['price'] = $detailObj->price;
    			$detail['productId'] = $detailObj->product_id;
    			$detail['count'] = $detailObj->count;

                //商品图片
                $image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$detailObj->product_id));
                if($image != null)
                {
                    $detail['img'] = $image->url;
                }
                else
                {
                    //加载默认图片
                    $product['img'] = "http://192.168.1.10/images/product_default.jpg";
                }
    			
    			$count += $detailObj->count;
    			$summary += ($detailObj->price * $detailObj->count);

    			$details[] = $detail;
    		}

    		$cart['details'] = $details;

    		$cart['count'] = $count;
			$cart['summary'] = $summary;

    		$carts[] = $cart;
    	}

    	$result['data'] = $carts;
		$result['success'] = true;

    	$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //购物车商品个数修改
    public function actionUpdateCount()
    {
    	$result = array('success'=>false);

    	$detailId = $_POST['id'];
    	$count = $_POST['count'];

    	$cartDetail = CartDetail::model()->findById($detailId);
    	if($cartDetail != null)
    	{
    		$cartDetail->count = $count;
    		$cartDetail->save();
    		$result['success'] = true;

    	}

    	$json = CJSON::encode($result);
        echo $json;
    }

    //商品删除
    public function actionRemove()
    {
    	$result = array('success'=>false);

    	$detailId = $_POST['id'];

    	$cartDetail = CartDetail::model()->findById($detailId);
    	if($cartDetail != null)
    	{
    		$cartDetail->count = $count;
    		$cartDetail->delete();
    		$result['success'] = true;

    	}

    	$json = CJSON::encode($result);
        echo $json;
    }

    //清空购物车 直接删除
    public function actionClear()
    {
    	$result = array('success'=>false);

    	$userId = $_POST['userid'];

    	$cartDetail = CartDetail::model()->findAll('user_id=:user_id', array(':user_id'=>$userId));
    	if($cartDetail != null)
    	{
    		$cartDetail->delete();

    	}
    	$result['success'] = true;

    	$json = CJSON::encode($result);
        echo $json;
    }

    //添加到购物车
    public function actionAdd()
    {
    	$result = array('success'=>false);

    	$userid = $_POST['userid'];
        $storeId = $_POST['storeId'];
    	$productId = $_POST['productId'];
        $price = $_POST['price'];
    	$count = $_POST['count'];

        $cart = Cart::model()->find('user_id=:user_id and store_id=:store_id', array(':user_id'=>$userid, ':store_id'=>$storeId));
        if($cart == null)
        {
            $cart = new Cart();

            $cart->user_id = $userid;
            $cart->store_id = $storeId;
            $cart->status = 1;
            $cart->create_time = time();

            $cart->save();

            $cartDetail = new CartDetail();
            $cartDetail->cart_id = $cart->id;
            $cartDetail->product_id = $productId;
            $cartDetail->price = $price;
            $cartDetail->count = $count;

            $product = Product::model()->findByPk($productId);
            $goods = Goods::model()->findByPk($product->goods_id);
            $cartDetail->goods_name = $goods->name;

            $cartDetail->save();
        }
        else
        {
            $cartDetail = CartDetail::model()->find('cart_id=:cart_id and product_id=:product_id', array(':cart_id'=>$cart->id, ':product_id'=>$productId));
            if($cartDetail != null)
            {
                //只修改数量
                $cartDetail->count = $count;
                $cartDetail->save();

            }
            else
            {
                $cartDetail = new CartDetail();
                $cartDetail->cart_id = $cart->id;
                $cartDetail->product_id = $productId;
                $cartDetail->price = $price;
                $cartDetail->count = $count;

                $product = Product::model()->findByPk($productId);
                $goods = Goods::model()->findByPk($product->goods_id);
                $cartDetail->goods_name = $goods->name;

                $cartDetail->save();
            }

        }

		$result['success'] = true;

    	$json = CJSON::encode($result);
        echo $json;
    }

    //生成订单
    public function actionCreateOrder()
    {
        $result = array('success'=>false);

        $id = $_POST['id'];

        $cart = Cart::model()->findByPk($id);
        $cartDetails = CartDetail::model()->findAll('cart_id=:cart_id', array(':cart_id'=>$cart->id));
        $store = Store::model()->findByPk($cart->store_id);

        $order = new Order();
        $order->create_time = time();
        $order->customer_id = $cart->user_id;
        $order->merchant_id = $store->user_id;
        $order->store_id = $cart->store_id;
        $order->status = 1;

        $order->save();
        
        $summary = 0;//总价

        //添加订单商品
        foreach ($cartDetails as $cartDetail)
        {
            $orderDetail = new OrderDetail();

            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $cartDetail->product_id;
            $orderDetail->count = $cartDetail->count;
            $orderDetail->price = $cartDetail->price;

            $orderDetail->save();
            $summary += $cartDetail->price;
        }

        $order->summary = $summary;
        $order->save();

        $result['success'] = true;

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