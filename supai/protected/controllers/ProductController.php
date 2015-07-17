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
				$recentBought['img'] = 'http://'.$_SERVER['SERVER_NAME'].$img->url;
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
			$product['id'] = $productObj->id;
			$product['goodsId'] = $productObj->goods_id;
			$product['alias'] = $productObj->alias;
			$product['additional'] = $productObj->description;
			$product['price'] = $productObj->price;
			$product['count'] = $productObj->count;
			$product['status'] = $productObj->status;
			$product['storeId'] = $productObj->store_id;

			if($productObj->goods_id != 0)
			{
				//有条码的商品
				$goodsObj = Goods::model()->findByPk($productObj->goods_id);
				if($goodsObj != null)
				{
					$product['name'] = $goodsObj->name;
					$product['rccode'] = $goodsObj->barcode;
					$product['description'] = $goodsObj->description;
					$product['origin'] = $goodsObj->origin;
					$product['merchant'] = $goodsObj->merchant;
					$product['merchant_code'] = $goodsObj->merchant_code;
				}

			}

			$product['favourite'] = 0;
			$productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
			if($productCollectObj != null)
			{
				$product['favourite'] = 1;
			}

			//商品图片
			$image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$id));
			if($image != null)
			{
				$product['img'] = 'http://'.$_SERVER['SERVER_NAME'].$image->url;
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

		$product->save();

		$productImg = new Image();
		$productImg->url = $imgUrl;
		$productImg->type = 1;
		$productImg->type_id = $product->id;
		$productImg->save();

		//返回商品属性
		$result['id'] = $product->id;
		$result['name'] = $goods->name;
		$result['alias'] = $product->alias;
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

	public function actionAddManually()
	{
		$result = array('success'=>false);

		$product = new Product();
		$product->goods_id = 0;

		$product->alias = $_POST['alias'];
		$product->description = $_POST['description'];
		$product->price = $_POST['price'];
		$product->store_id = $_POST['storeId'];
		$product->status = 1;

		$product->save();

		$imgUrl = $_POST['img'];

		$productImg = new Image();
		$productImg->url = $imgUrl;
		$productImg->type = 1;
		$productImg->type_id = $product->id;
		$productImg->save();

		//返回商品属性
		$result['id'] = $product->id;
		$result['alias'] = $product->alias;
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
				$store = Store::model()->findByPk($productObj->store_id);
				if($store != null)
				{
					$product = array();

					$product['id'] = $productObj->id;
					$product['name'] = $goods->name;
					$product['alias'] = $productObj->alias;
					$product['origin'] = $goods->origin;
					$product['merchant_code'] = $goods->merchant_code;
					$product['merchant'] = $goods->merchant;
					$product['price'] = $productObj->price;
					$product['count'] = $productObj->count;
					$product['store_id'] = $productObj->store_id;

					
					$product['store_name'] = $store->name;
					$product['address'] = $store->address;

					//商品图片
					$image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$productObj->id));
					if($image != null)
					{
						$product['img'] = 'http://'.$_SERVER['SERVER_NAME'].$image->url;
					}
					else
					{
						//加载默认图片
						$product['img'] = 'http://'.$_SERVER['SERVER_NAME']."/images/product_default.jpg";
					}
					$product['favourite'] = 0;
					$productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
					if($productCollectObj != null)
					{
						$product['favourite'] = 1;
					}

					$result[] = $product;
				}

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
		if(isset($_POST['img']))
		{
			$imgUrl = $_POST['img'];

			$image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$id));
			if($image != null)
			{
				$image->url = $imgUrl;
				$image->save();
			}
		}
		

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

		$product = Product::model()->findByPk($productId);
		if($product != null)
		{
			$productCollect = ProductCollect::model()->find('user_id=:user_id and product_id=:product_id', array(':user_id'=>$userid, ':product_id'=>$productId));
			if($productCollect == null)
			{
				$productCollect = new ProductCollect();

				$productCollect->user_id = $userid;
				$productCollect->product_id = $productId;
				
				$storeCollect = StoreCollect::model()->find('user_id=:user_id and store_id=:store_id', array(':user_id'=>$userid, ':store_id'=>$product->store_id));
				if($storeCollect != null)
				{
					$productCollect->store_collect_id = $storeCollect->id;

				}
				else
				{
					$productCollect->store_collect_id = 0;

				}

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

	//查询商品 根据名称
	public function actionSearchByName()
	{
		$result = array();

		$alias = $_POST['alias'];
		$storeId = 0;
		if(isset($_POST['storeId']))
		{
			$storeId = $_POST['storeId'];
		}

		if($storeId == 0)
		{
			$productObjs = Product::model()->findAll("`alias` like :alias", array(":alias"=>"%".$alias."%"));

		}
		else
		{
			$productObjs = Product::model()->findAll("`alias` like :alias and store_id=:store_id", array(":alias"=>"%".$alias."%", ":store_id"=>$storeId));

		}

		foreach ($productObjs as $productObj)
		{
			$store = Store::model()->findByPk($productObj->store_id);
			if($store == null)
			{
				continue;
			}

			$product = array();
			$goods = Goods::model()->findByPk($productObj->goods_id);
			$product['id'] = $productObj->id;
			$product['alias'] = $productObj->alias;
			
			$product['price'] = $productObj->price;
			$product['count'] = $productObj->count;
			$product['store_id'] = $productObj->store_id;

			if($goods != null)
			{
				$product['name'] = $goods->name;
				$product['origin'] = $goods->origin;
				$product['merchant_code'] = $goods->merchant_code;
				$product['merchant'] = $goods->merchant;
			}
			else
			{
				$product['name'] = $productObj->alias;
				$product['origin'] = "";
				$product['merchant_code'] = "";
				$product['merchant'] = "";

			}

			$product['store_name'] = $store->name;
			$product['address'] = $store->address;

			//商品图片
			$image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$productObj->id));
			if($image != null)
			{
				$product['img'] = 'http://'.$_SERVER['SERVER_NAME'].$image->url;
			}
			else
			{
				//加载默认图片
				$product['img'] = 'http://'.$_SERVER['SERVER_NAME']."/images/product_default.jpg";
			}
			$productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
			$product['favourite'] = 0;
			if($productCollectObj != null)
			{
				$product['favourite'] = 1;
			}

			$result[] = $product;

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