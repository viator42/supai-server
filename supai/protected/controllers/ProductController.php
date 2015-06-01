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
		$recentBoughtObjs = RecentBought::model()->findAll('user_id = :userid and status != 0', array(':userid'=>$id));

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
			$pruduct['status'] = $productObj->status;
			$product['additional'] = $productObj->description;
			$product['count'] = $productObj->count;

			//商品图片
			$image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$id));
			if($image != null)
			{
				$product['img'] = $image->url;
			}
			else
			{
				//加载默认图片
				$product['img'] = 'http://'.$_SERVER['SERVER_NAME']."/images/product_default.jpg";
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

		//添加图片
		$imgUrl = $_POST['img'];

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

			$goodsImg = new Image();
			$goodsImg->url = $imgUrl;
			$goodsImg->type = 2;
			$goodsImg->type_id = $goods->id;
			$goodsImg->save();

		}

		$product = new Product();
		$product->alias = $_POST['alias'];
		$product->goods_id = $goods->id;
		$product->price = $_POST['price'];
		$product->store_id = $_POST['storeId'];
		$product->count = $_POST['count'];

		$product->save();

		$productImg = new Image();
		$productImg->url = $imgUrl;
		$productImg->type = 1;
		$productImg->type_id = $product->id;
		$productImg->save();

		//返回商品属性
		$result['id'] = $product->id;
		$result['name'] = $goods->name;
		$result['alias'] = $goods->alias;
		$result['rccode'] = $goods->barcode;
		$result['description'] = $goods->description;
		$result['origin'] = $goods->origin;
		$result['merchant'] = $goods->merchant;
		$result['merchant_code'] = $goods->merchant_code;
		$result['price'] = $product->price;
		$result['storeId'] = $product->store_id;
		$result['price'] = $product->price;
		$result['status'] = $product->status;
		$result['additional'] = $product->description;
		$result['img'] = $imgUrl;

		$result['success'] = true;
		$json = str_replace("\\/", "/", CJSON::encode($result));
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
			$productObjs = Product::model()->findAll('goods_id=:goods_id and status != 0', array(':goods_id'=>$goods->id));
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

				//商品图片
				$image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$productObj->id));
				if($image != null)
				{
					$product['img'] = $image->url;
				}
				else
				{
					//加载默认图片
					$product['img'] = 'http://'.$_SERVER['SERVER_NAME']."/images/product_default.jpg";
				}

				$result[] = $product;

			}

		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//更新商品
	public function actionUpdate()
	{
		$result = array('success'=>false);

		$id = $_POST['id'];
		$description = $_POST['description'];
		$price = $_POST['price'];
		$count = $_POST['count'];
		$status = $_POST['status'];
		$alias = $_POST['alias'];

		$product = Product::model()->findByPk($id);
		if($product != null)
		{
			$product->price = $price;
			$product->count = $count;
			$product->description = $description;
			$product->status = $status;
			$product->alias = $alias;

			$product->save();
			$result['product'] = $product;
			$result['success'] = true;
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//收藏商品
	public function actionAddFavourite()
	{
		$result = array('success'=>false);

		$userid = $_POST['userid'];
		$productId = $_POST['productId'];

		if(isset($_POST['storeCollectId']))
		{
			$storeCollectId = $_POST['storeCollectId'];
			$productCollect = ProductCollect::model()->find('user_id=:user_id and product_id=:product_id and store_collect_id=:store_collect_id', array(':user_id'=>$userid, ':product_id'=>$productId, ':store_collect_id'=>$storeCollectId));
			if($productCollect == null)
			{
				$productCollect = new ProductCollect();

				$productCollect->user_id = $userid;
				$productCollect->product_id = $productId;
				$productCollect->store_collect_id = $storeCollectId;

				$productCollect->save();	
				$result['success'] = true;
			}

		}
		else
		{
			$productCollect = ProductCollect::model()->find('user_id=:user_id and product_id=:product_id', array(':user_id'=>$userid, ':product_id'=>$productId));
			if($productCollect == null)
			{
				$productCollect = new ProductCollect();

				$productCollect->user_id = $userid;
				$productCollect->product_id = $productId;
				$productCollect->store_collect_id = 0;

				$productCollect->save();	
				$result['success'] = true;
			}
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//取消收藏商品
	public function actionUnfavourite()
	{
		$result = array('success'=>false);

		$userid = $_POST['userid'];
		$productId = $_POST['productId'];

		$productCollect = ProductCollect::model()->find('user_id=:user_id and product_id=:product_id', array(':user_id'=>$userid,':product_id'=>$productId));
		if($productCollect != null)
		{
			$productCollect->delete();
			$result['success'] = true;
		}

		// $storeCollect->delete();

		// 	$result['success'] = true;

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//删除商品
	public function actionDelete()
	{
		$result = array('success'=>false);

		$id = $_POST['id'];

		$product = Product::model()->findByPk($id);
		if($product != null)
		{
			$product->status = 0;
			$product->save();
			$result['success'] = true;
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