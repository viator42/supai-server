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
		
		$store->save();

		$result['data'] = $store;
		$result['success'] = true;

		$json = CJSON::encode($result);
        echo $json;

	}

	//店铺详情
	public function actionStoreProducts()
	{
		$result = array('success'=>false);

		$data = array();
		$products = array();

		$storeId = $_POST['id'];

		$storeObj = Store::model()->findByPk($storeId);

		if($store != null)
		{
			$data['logo'] = $storeObj->logo;
			$data['name'] = $storeObj->name;
			$data['description'] = $storeObj->description;
			$data['address'] = $storeObj->address;


		}

		$json = CJSON::encode($result);
        echo $json;
	}

	//返回商店的所有商品列表
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