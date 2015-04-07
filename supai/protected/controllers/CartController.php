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

    		$cart = new array();
    		$details = new array();

    		$detailObjs = CartDetail::model->findAll('cart_id=:cart_id', array(':cart_id'=>$cartObj->id));
    		foreach ($detailObjs as $detailObj)
    		{
    			$detail = new array();
    			
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

    //商品删除

    //添加到购物车

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