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
		$result = array('success'=>false);

		$barcode = $_POST['barcode'];
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
				$images[] = 'http://'.$_SERVER['SERVER_NAME'].$imageObj->url;

			}
			$result['images'] = $images;

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