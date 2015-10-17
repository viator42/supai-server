<?php

class GoodsController extends Controller
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

	//商品详情	
	public function actionDetail()
	{
		$result = array('success'=>false);

		$id = $_GET['id'];
		$goods = Goods::model()->findByPk($id);
		if($goods != null)
		{
			$result['success'] = true;

		}


		$json = CJSON::encode($result);
        echo $json;
	}

	//根据条形码查询商品信息
	public function actionSearch()
	{
		$result = array('success'=>false, 'exist'=>false);

		$barcode = $_POST['barcode'];
        $storeId = $_POST['storeId'];

		$goods = Goods::model()->find('barcode=:barcode', array(':barcode'=>$barcode));
		if($goods != null)
		{
			$result['barcode'] = $goods->barcode;
			$result['name'] = $goods->name;
			$result['priceInterval'] = $goods->price_interval;
			$result['origin'] = $goods->origin;
			$result['merchantCode'] = $goods->merchant_code;
			$result['merchant'] = $goods->merchant;

			//查询商品图片
			$images = array();
			$imageObjs = Image::model()->findAll('type=2 and type_id=:type_id', array(':type_id'=>$goods->id));
			foreach ($imageObjs as $imageObj) 
			{
				if($imageObj != null)
				{
					$images[] = $imageObj->url;
				}

			}
			$result['images'] = $images;

            $productObj = Product::model()->find('goods_id=:goods_id and store_id=:store_id', array(':goods_id'=>$goods->id, ':store_id'=>$storeId));
            $store = Store::model()->findByPk($productObj->store_id);
            if($productObj != null && $store != null)
            {
                $result['exist'] = true;

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
                $product['status'] = $productObj->status;

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
                $product['favourite'] = 0;
                $productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
                if($productCollectObj != null)
                {
                    $product['favourite'] = 1;
                }

                $result['product'] = $product;
            }

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