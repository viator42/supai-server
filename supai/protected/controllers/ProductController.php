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

		foreach($recentBoughtObjs as $recentBoughtObj) {
            $product = array();

            $productObj = Product::model()->findByPk($recentBoughtObj->product_id);
            if ($productObj != null) {
                $img = Image::model()->find('type = :IMAGE_TYPE_PRODUCT and type_id = :type_id',
                    array(':IMAGE_TYPE_PRODUCT' => StaiticValues::$IMAGE_TYPE_PRODUCT, ':type_id' => $productObj->id));
                if ($img != null) {
                    $product['img'] = $img->url;
                } else {
                    //使用默认商品图片
                    $product['img'] = "/images/product_default.jpg";
                }

                $product['goods_id'] = $productObj->goods_id;
                $product['id'] = $productObj->id;
                $product['alias'] = $productObj->alias;
                $product['additional'] = $productObj->description;
                $product['price'] = $productObj->price;
                $product['count'] = $productObj->count;
                $product['status'] = $productObj->status;
                $product['store_id'] = $productObj->store_id;

                if ($productObj->goods_id != 0) {
                    $goodsObj = Goods::model()->findByPk($productObj->goods_id);
                    if ($goodsObj != null) {
                        $product['goods_id'] = $productObj->goods_id;
                        $product['name'] = $goodsObj->name;
                        $product['rccode'] = $goodsObj->barcode;
                        $product['description'] = $goodsObj->description;
                        $product['origin'] = $goodsObj->origin;
                        $product['merchant'] = $goodsObj->merchant;
                        $product['merchant_code'] = $goodsObj->merchant_code;
                    }
                }

                $product['favourite'] = StaiticValues::$FAVOURITE;

                if ($productObj->status != StaiticValues::$PRODUCT_STATUS_REMOVED) {
                    $result[] = $product;
                }

            }
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

			$product['favourite'] = StaiticValues::$UNFAVOURITE;
			$productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
			if($productCollectObj != null)
			{
				$product['favourite'] = StaiticValues::$FAVOURITE;
			}

			//商品图片
			$image = Image::model()->find('type=:IMAGE_TYPE_PRODUCT and type_id=:type_id', array(':IMAGE_TYPE_PRODUCT'=>StaiticValues::$IMAGE_TYPE_PRODUCT, ':type_id'=>$id));
			if($image != null)
			{
				$product['img'] = $image->url;
			}
			else
			{
				//加载默认图片
				$product['img'] = "/images/product_default.jpg";
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
			$goodsImg->type = StaiticValues::$IMAGE_TYPE_GOODS;
			$goodsImg->type_id = $goods->id;
			$goodsImg->save();

		}

		$product = new Product();
		$product->alias = $_POST['alias'];
        $product->description = $_POST['additional'];
		$product->goods_id = $goods->id;
		$product->price = $_POST['price'];
		$product->store_id = $_POST['storeId'];
        $product->count = $_POST['count'];

		$product->save();

		$productImg = new Image();
		$productImg->url = $imgUrl;
		$productImg->type = StaiticValues::$IMAGE_TYPE_PRODUCT;
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
        $result['count'] = $product->count;

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
		$product->status = StaiticValues::$PRODUCT_STATUS_ENABLE;
        $product->count = $_POST['count'];

		$product->save();

		$imgUrl = $_POST['img'];

		$productImg = new Image();
		$productImg->url = $imgUrl;
		$productImg->type = StaiticValues::$IMAGE_TYPE_PRODUCT;
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
        $result['count'] = $product->count;
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
        $page = $_POST['page'];
        $limit = (int)$_POST['limit'];	//每页的个数
        
		$storeId = 0;
		if(isset($_POST['storeId']))
		{
			$storeId = $_POST['storeId'];
		}

		$goods = Goods::model()->find('barcode=:barcode', array(':barcode'=>$barcode));
		if($goods != null)
		{
			if($storeId != 0)
			{
				$productObjs = Product::model()->findAll('goods_id=:goods_id and store_id=:store_id and status != :PRODUCT_STATUS_REMOVED limit :offset, :limit',
                    array(':goods_id'=>$goods->id, ':store_id'=>$storeId, ':PRODUCT_STATUS_REMOVED'=>StaiticValues::$PRODUCT_STATUS_REMOVED, ':offset'=>($page * $limit), ':limit'=>$limit));

			}
			else
			{
				$productObjs = Product::model()->findAll('goods_id=:goods_id and status != :PRODUCT_STATUS_REMOVED limit :offset, :limit',
                    array(':goods_id'=>$goods->id, ':PRODUCT_STATUS_REMOVED'=>StaiticValues::$PRODUCT_STATUS_REMOVED, ':offset'=>($page * $limit), ':limit'=>$limit));

			}
			
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
					$product['additional'] = $productObj->description;
					
					$product['store_name'] = $store->name;
					$product['address'] = $store->address;

					//商品图片
					$image = Image::model()->find('type=:IMAGE_TYPE_PRODUCT and type_id=:type_id',
                        array(':IMAGE_TYPE_PRODUCT'=>StaiticValues::$IMAGE_TYPE_PRODUCT, ':type_id'=>$productObj->id));
					if($image != null)
					{
						$product['img'] = $image->url;
					}
					else
					{
						//加载默认图片
						$product['img'] = "/images/product_default.jpg";
					}
					$product['favourite'] = StaiticValues::$UNFAVOURITE;
					$productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
					if($productCollectObj != null)
					{
						$product['favourite'] = StaiticValues::$FAVOURITE;
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
			$product->status = StaiticValues::$PRODUCT_STATUS_REMOVED;
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

        $page = $_POST['page'];
        $limit = (int)$_POST['limit'];	//每页的个数

		if($storeId == 0)
		{
			$productObjs = Product::model()->findAll("`alias` like :alias limit :offset, :limit",
                array(":alias"=>"%".$alias."%", ':offset'=>($page * $limit), ':limit'=>$limit));

		}
		else
		{
			$productObjs = Product::model()->findAll("`alias` like :alias and store_id=:store_id limit :offset, :limit",
                array(":alias"=>"%".$alias."%", ":store_id"=>$storeId, ':offset'=>($page * $limit), ':limit'=>$limit));

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
			$product['additional'] = $productObj->description;
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
				$product['img'] = $image->url;
			}
			else
			{
				//加载默认图片
				$product['img'] = "/images/product_default.jpg";
			}
			$productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
			$product['favourite'] = StaiticValues::$UNFAVOURITE;
			if($productCollectObj != null)
			{
				$product['favourite'] = StaiticValues::$FAVOURITE;
			}

			$result[] = $product;

		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

    //商品数量增加
    public function actionCountIncrease()
    {
        $result = array('success'=>false);

        $productId = $_POST['productId'];
        $count = $_POST['count'];

        $product = Product::model()->findByPk($productId);
        if($product != null)
        {
            $product->count += $count;
            $product->save();

            $result['success'] = true;
            $result['count'] = $product->count;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //查询条码商品是否存在
    public function actionBarcodeExist()
    {
        $result = array('success'=>false);

        $barcode = $_POST['barcode'];
        $storeId = $_POST['storeId'];

        $product = Product::model()->find('store_id=:store_id and ');

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    /*
	//多商品添加
	public function actionMultiAdd()
	{
		$result = array('success'=>false);

		for ($i = 1; $i <= 10000; $i++)
		{
			$product = new Product();

		  	$product->goods_id = 0;

			$product->alias = "测试商品_".$i;
			$product->description = "测试商品描述_".$i;
			$product->price = 123;
			$product->store_id = 1;
			$product->status = 1;

			$product->save();

			$imgUrl = "/images/product_default.jpg";

			$productImg = new Image();
			$productImg->url = $imgUrl;
			$productImg->type = 1;
			$productImg->type_id = $product->id;
			$productImg->save();

		}

		$result['success'] = true;
		$json = str_replace("\\/", "/", CJSON::encode($result));
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