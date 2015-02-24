<?php
/*
地区控制器
*/
class AreaController extends Controller
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
    

	public function actionIndex()
	{
		$this->render('index');
	}

	//省列表
	public function actionProvinces()
	{
		$result = array();

		$areas = Area::model()->findAll('level = 0');

		foreach ($areas as $area) 
		{
			$result[] = $area;

		}

		$json = CJSON::encode($result);
        echo $json;
	}

	//根据code查找下一级
	public function actionChildren()
	{
		$result = array();

		$code = $_GET['code'];

		$cities = Area::model()->findAll('p_code=:p_code', array(':p_code' => $code));

		foreach ($cities as $city) 
		{
			$result[] = $city;

		}

		$json = CJSON::encode($result);
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