<?php

class CollectController extends Controller
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

    //返回用户收藏的店铺/商品
    public function actionAll()
    {
    	$result = array();

		$userid = $_POST['userid'];

		//收藏的店铺
		$stores = array();
		$storeCollectObjs = StoreCollect::model()->findAll('user_id=:user_id', array(':user_id'=>$userid));
		foreach ($storeCollectObjs as $storeCollectObj)
		{
			$store = array();
			$storeObj = Store::model()->findByPk($storeCollectObj->store_id);
			$store['id'] = $storeObj->id;
			$store['logo'] = $storeObj->logo;
			$store['name'] = $storeObj->name;
			$store['userId'] = $storeObj->user_id;
			$store['address'] = $storeObj->address;
			$store['description'] = $storeObj->description;
            $store['longitude'] = $storeObj->longitude;
            $store['latitude'] = $storeObj->latitude;
            $store['favourite'] = 1;
            $store['status'] = $storeObj->status;

			//下属商品
			$products = array();
			$productCollectObjs = ProductCollect::model()->findAll('store_collect_id=:store_collect_id', array(':store_collect_id'=>$storeCollectObj->id));
			foreach ($productCollectObjs as $productCollectObj)
			{
				$product = array();
				$productObj = Product::model()->findByPk($productCollectObj->product_id);
				$img = Image::model()->find('type = 1 and type_id = :type_id', array(':type_id'=>$productObj->id));
				
				if($productObj->goods_id != 0)
				{
					$goodsObj = Goods::model()->findByPk($productObj->goods_id);
					$product['goodsId'] = $productObj->goods_id;
					$product['id'] = $productObj->id;
					$product['name'] = $goodsObj->name;
					$product['alias'] = $productObj->alias;
					$product['img'] = $img->url;
					$product['rccode'] = $goodsObj->barcode;
					$product['description'] = $goodsObj->description;
					$product['origin'] = $goodsObj->origin;
					$product['merchant'] = $goodsObj->merchant;
					$product['merchant_code'] = $goodsObj->merchant_code;
					$product['price'] = $productObj->price;
					$product['storeId'] = $productObj->store_id;
					$product['price'] = $productObj->price;
					$product['status'] = $productObj->status;
					$product['additional'] = $productObj->description;
					$product['count'] = $productObj->count;

				}
				else
				{
					$product['goodsId'] = $productObj->goods_id;
					$product['id'] = $productObj->id;
					$product['alias'] = $productObj->alias;
					$product['additional'] = $productObj->description;
					$product['price'] = $productObj->price;
					$product['count'] = $productObj->count;
					$product['status'] = $productObj->status;
					$product['storeId'] = $productObj->store_id;

				}
				$product['favourite'] = 1;

				if($productObj->status != 0)
				{
					$products[] = $product;
				}
				
			}
			$store['products'] = $products;

			//店铺开店时才能搜索到
			if($storeObj->status == 1)
			{
				$stores[] = $store;

			}
			
		}

		$result['stores'] = $stores;

		//不隶属店铺的商品
		$defaults = array();
		$productCollectObjs = ProductCollect::model()->findAll('store_collect_id = 0 and user_id=:user_id', array(':user_id'=>$userid));
		foreach ($productCollectObjs as $productCollectObj)
		{
			$product = array();
			$productObj = Product::model()->findByPk($productCollectObj->product_id);
			$img = Image::model()->find('type = 1 and type_id = :type_id', array(':type_id'=>$productObj->id));

			if($productObj->goods_id != 0)
			{
				$goodsObj = Goods::model()->findByPk($productObj->goods_id);
				$product['goodsId'] = $productObj->goods_id;
				$product['id'] = $productObj->id;
				$product['name'] = $goodsObj->name;
				$product['alias'] = $productObj->alias;
				$product['img'] = $img->url;
				$product['rccode'] = $goodsObj->barcode;
				$product['description'] = $goodsObj->description;
				$product['origin'] = $goodsObj->origin;
				$product['merchant'] = $goodsObj->merchant;
				$product['merchant_code'] = $goodsObj->merchant_code;
				$product['price'] = $productObj->price;
				$product['storeId'] = $productObj->store_id;
				$product['price'] = $productObj->price;
				$product['status'] = $productObj->status;
				$product['additional'] = $productObj->description;
				$product['favourite'] = 1;
				$product['count'] = $productObj->count;

			}
			else
			{
				$product['goodsId'] = $productObj->goods_id;
				$product['id'] = $productObj->id;
				$product['alias'] = $productObj->alias;
				$product['additional'] = $productObj->description;
				$product['price'] = $productObj->price;
				$product['count'] = $productObj->count;
				$product['status'] = $productObj->status;
				$product['storeId'] = $productObj->store_id;

			}

			$img = Image::model()->find('type = 1 and type_id = :type_id', array(':type_id'=>$productObj->id));

			if($productObj->status != 0)
			{
				$defaults[] = $product;
			}
			
		}

		$result['defaults'] = $defaults;

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