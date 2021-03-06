<?php

class AppController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    // 获取应用信息
    public function actionInfo()
    {
        $result = array();

        $result['versionName'] = StaiticValues::$versionName;
        $result['versionCode'] = StaiticValues::$versionCode;

        $json = CJSON::encode($result);
        echo $json;
    }

    public function actionEnvinfo()
    {
        phpinfo();

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