<?php

//商品控制器
class ProductController extends Controller
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

	//最近购买的商品列表
	public function actionRecent()
	{
		$result = array();

		$id = $_POST['userid'];
		$recentBoughtObjs = RecentBought::model()->findAll('user_id = :userid', array(':userid'=>$id));

		foreach($recentBoughtObjs as $recentBoughtObj) 
		{
			$recentBought = array();

			$product = Product::model()->findByPk($recentBoughtObj->product_id);
			if($product != null)
			{
				$goods = Goods::model()->findByPk($product->id);
				$recentBought['id'] = $product->id;
				$recentBought['name'] = $goods->name;
				$recentBought['img'] = "";
			}



		// 	$lastBoughtProduct = array();

		// 	$product = SaleProduct::model()->findByPk($recentBought->product_id);

		// 	$lastBoughtProduct['id'] = $recentBought->product_id;
		// 	$lastBoughtProduct['name'] = $product->name;

		// 	$result[] = $lastBoughtProduct;

			$result[] = $recentBought;
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}
	
	
	//查询商品详情
	public function actionDetail()
	{
		$result = array('success'=>false);
		$product = array();

		$id = $_POST['id'];

		$productObj = Product::model()->findByPk($id);
		if($productObj != null)
		{
			$goodsObj = Goods::model()->findByPk($productObj->goods_id);
			$product['name'] = $goodsObj->name;
			$product['rccode'] = $goodsObj->rccode;
			$product['description'] = $goodsObj->description;
			$product['origin'] = $goodsObj->origin;
			$product['merchant'] = $goodsObj->merchant;
			$product['merchant_code'] = $goodsObj->merchant_code;
			$product['price'] = $productObj->price;
			$product['additional'] = $productObj->description;

			//商品图片
			//$images = Image::model()->findAll();
			$product['img'] = "";

			$result['data'] = $product;
			$result['success'] = true;
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