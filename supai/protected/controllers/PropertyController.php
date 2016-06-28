<?php

class PropertyController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    //添加商品时的属性列表
    public function actionCategories()
    {
        $result = array();

        $storeId = $_POST['storeId'];

        $propertyCatObjs = PropertyCategory::model()->findAll('store_id=:store_id or type=1', array(':store_id'=>$storeId));

        foreach($propertyCatObjs as $propertyCatObj)
        {
            $propertyCat = array();

            $propertyCat['id'] = $propertyCatObj->id;
            $propertyCat['name'] = $propertyCatObj->name;
            $propertyCat['type'] = $propertyCatObj->type;
            $propertyCat['storeId'] = $propertyCatObj->store_id;
            $propertyCat['status'] = $propertyCatObj->status;

            $result[] = $propertyCat;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //查询商品的属性信息

    //添加自定义属性
    public function actionAddProperty()
    {
        $result = array('success'=>false);

        $orderId = $_POST['orderId'];


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