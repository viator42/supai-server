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

    		$detailObjs = CartDetail::model()->findAll('cart_id=:cart_id', array(':cart_id'=>$cartObj->id));
    		foreach ($detailObjs as $detailObj)
    		{
    			$detail = array();
    			
    			$detail['name'] = $detailObj->goods_name;
    			$detail['price'] = $detailObj->price;
    			$detail['productId'] = $detailObj->product_id;
    			$detail['count'] = $detailObj->count;
    			
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

    	$json = CJSON::encode($result);
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

    	$cartid = $_POST['cartid'];
    	$productId = $_POST['productid'];
    	$count = $_POST['count'];

    	$product = Product::model()->findByPk($productId);
    	$goods = Goods::model()->findByPk($product->id);

    	$detail = new CartDetail();

		$detail->cart_id = $cartid; 
		$detail->goods_name = $goods->name; 	
		$detail->price = $product->price; 
		$detail->product_id = $productId;
		$detail->count = $count;

    	$detail->save();

    	$result['data'] = $detail;
		$result['success'] = true;

    	$json = CJSON::encode($result);
        echo $json;
    }

    //

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