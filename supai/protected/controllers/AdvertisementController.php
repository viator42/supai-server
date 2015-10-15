<?php

class AdvertisementController extends Controller
{
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

    //单个广告页
	public function actionIndex()
	{
		$this->render('index');
	}

    //所有广告列表
    public function actionAll()
	{
        $result = array();

        $advertisementObjs = Advertisement::model()->findAll();
        foreach($advertisementObjs as $advertisementObj)
        {
            $result[] = $advertisementObj;

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