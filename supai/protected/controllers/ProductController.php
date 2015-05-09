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
				$img = Image::model()->find('type = 1 and type_id = :type_id', array(':type_id'=>$product->id));
				$recentBought['img'] = $img->url;
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
			$product['rccode'] = $goodsObj->barcode;
			$product['description'] = $goodsObj->description;
			$product['origin'] = $goodsObj->origin;
			$product['merchant'] = $goodsObj->merchant;
			$product['merchant_code'] = $goodsObj->merchant_code;
			$product['price'] = $productObj->price;
			$product['storeId'] = $productObj->store_id;
			$product['price'] = $productObj->price;
			
			$product['additional'] = $productObj->description;

			//商品图片
			$image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$id));
			if($image != null)
			{
				$product['img'] = $image->url;
			}
			else
			{
				//加载默认图片
				$product['img'] = "http://192.168.1.10/images/product_default.jpg";
			}
			

			$result['data'] = $product;
			$result['success'] = true;
		}
		
		

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//商品添加
	public function actionAdd()
	{
		$result = array('success'=>false);

		$barcode = $_POST['barcode'];

		//查看goods是否存在
		$goods = Goods::model()->find('barcode=:barcode', array(':barcode'=>$barcode));
		if($goods == null)
		{
			$goods = new Goods();
			$goods->name = $_POST['name'];
			$goods->barcode = $_POST['barcode'];
			$goods->price_interval = $_POST['priceInterval'];
			$goods->origin = $_POST['origin'];
			$goods->merchant_code = $_POST['merchantCode'];
			$goods->merchant = $_POST['merchant'];

			$goods->save();
		}

		$product = new Product();
		$product->goods_id = $goods->id;
		$product->price = $_POST['price'];
		$product->store_id = $_POST['storeId'];

		$product->save();

		$result['data'] = $product;

		//添加图片
		$imgUrl = $_POST['img'];
		$img = new Image();
		$img->type = 1;
		$img->type_id = $product->id;
		$img->url = $imgUrl;

		$img->save();

		$result['success'] = true;

		$json = CJSON::encode($result);
        echo $json;
	}

	//扫码查询商品,返回同意area的所有商家的商品.
	public function actionSearch()
	{
		$result = array();

		$barcode = $_POST['barcode'];

		$goods = Goods::model()->find('barcode=:barcode', array(':barcode'=>$barcode));
		if($goods != null)
		{
			$productObjs = Product::model()->findAll('goods_id=:goods_id', array(':goods_id'=>$goods->id));
			foreach ($productObjs as $productObj)
			{
				$product = array();

				$product['id'] = $productObj->id;
				$product['name'] = $goods->name;
				$product['origin'] = $goods->origin;
				$product['merchant_code'] = $goods->merchant_code;
				$product['merchant'] = $goods->merchant;
				$product['price'] = $productObj->price;
				$product['count'] = $productObj->count;
				$product['store_id'] = $productObj->store_id;

				$store = Store::model()->findByPk($product['store_id']);
				$product['store_name'] = $store->name;
				$product['address'] = $store->address;

				$result[] = $product;

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