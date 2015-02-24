<?php

class UserController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	//登录
	public function actionLogin()
	{
		$result = array('success'=>false,
                         'err_msg'=>'',
                         'data'=>null);

		if(isset($_POST['username']) && isset($_POST['password']))
		{
			$username = $_POST['username'];
			$password = $_POST['password'];

			

			result['success'] = true;
		}
		else
		{
			result['err_msg'] = "参数错误";
			result['success'] = false;

		}

		$json = CJSON::encode($result);
        echo $json;

		


	}

	//注册
	public function actionRegister()
	{

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