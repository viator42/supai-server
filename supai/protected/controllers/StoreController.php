<?php

class StoreController extends Controller
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

	//创建店铺
	public function actionNew()
	{
		$result = array('success'=>false);

		$store = new Store();

		$store->user_id = $_POST['userid'];
		$store->name = $_POST['name'];
		$store->address = $_POST['address'];
		$store->description = $_POST['description'];
		$store->logo = $_POST['logo'];
		
		$areaCode = $_POST['area'];
		$area = Area::model()->find('code=:code', array(':code'=>$areaCode));
		$store->area_id = $area->id;

		$store->save();

		$result['data'] = $store;
		$result['success'] = true;

		$json = CJSON::encode($result);
        echo $json;

	}

	//查询用户的店铺信息
	public function actionInfo()
	{
		$result = array('success'=>false);

		$userid = $_POST['userid'];

		$storeObj = Store::model()->find('user_id=:user_id', array(':user_id'=>$userid));
		if($storeObj != null)
		{
			$result['data'] = $storeObj;
			$result['success'] = true;

		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}


	//返回商店的所有商品列表
	public function actionProducts()
	{
		$result = array();

		$data = array();
		$products = array();

		$storeId = $_POST['id'];

		$productObjs = Product::model()->findAll('store_id=:store_id', array(':store_id'=>$storeId));
		foreach ($productObjs as $productObj) 
		{
			$pruduct = array();
			$pruduct['id'] = $productObj->id;
			$goods = Goods::model()->findByPk($productObj->goods_id);
			$pruduct['name'] = $goods->name;
			$pruduct['price'] = $productObj->price;


			$result[] = $pruduct;

		}
		

		$json = CJSON::encode($result);
        echo $json;
	}

	
	/*
	public function actionStoreProducts()
	{
		$result = array('success'=>false);

		$products = array();

		$storeId = $_POST['id'];

		$productObjs = Product::model->findAll('store_id=:store_id', array(':store_id'=>$storeId));

		foreach ($productObjs as $productObj) 
		{
			$product = array();
			$product['id'] = $productObj->id;
			$product['price'] = $productObj->price;

			$goodsObj = Goods::model->findByPk($productObj->goods_id);
			$product['name'] = $productObj->price;
			$product['rccode'] = $productObj->rccode;

			$products[] = $product;

		}

		$result['data'] = $products;

		$json = CJSON::encode($result);
        echo $json;
		
	}
	*/

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