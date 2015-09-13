<?php

class StatisticsController extends Controller
{
//	public function actionIndex()
//	{
//		$this->render('index');
//	}

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

    //返回库存量少的商品列表
    public function actionProductLowInStore()
    {
        $result = array();

        $storeId = $_POST['storeId'];
        $store = Store::model()->findByPk($storeId);

        $productObjs = Product::model()->findAll('store_id=:store_id and status != 0 and count <= :count order by count desc',
            array(':store_id'=>$storeId, ':count'=>$store->storage_warning));
        foreach ($productObjs as $productObj)
        {
            $product = array();

            $product['id'] = $productObj->id;
            $product['alias'] = $productObj->alias;
            $product['price'] = $productObj->price;
            $product['count'] = $productObj->count;
            $product['status'] = $productObj->status;

            $img = Image::model()->find('type = 1 and type_id = :type_id', array(':type_id'=>$productObj->id));
            if($img != null)
            {
                $product['img'] = $img->url;
            }
            else
            {
                $product['img'] = "/images/product_default.jpg";
            }

            $result[] = $product;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //商品盘点
    public function actionProductParticulars()
    {
        $result = array();

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