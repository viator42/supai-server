<?php

class UserController extends Controller
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


	//登录
	public function actionLogin()
	{
		$result = array('success'=>false);

		$tel = $_POST['tel'];
		$password = $_POST['password'];

		$model = User::model()->find('tel = :tel and password = :password', array(':tel'=>$tel, ':password'=>$password));

		if($model != null)
		{
			$result['data'] = $model;				
			$result['success'] = true;
		}

		$json = CJSON::encode($result);
        echo $json;

	}

	//注册
	public function actionRegister()
	{
		$result = array('success'=>false);

		$tel = $_POST['tel'];
		$password = $_POST['password'];

		$model = new User();

		$model->username = $tel;
		$model->tel = $tel;
		$model->password = $password;
		$model->register_time = time();

		$model->save();

		$result['data'] = $model;
		$result['success'] = true;

		$json = CJSON::encode($result);
        echo $json;

	}
	
/*
	//完善用户信息
	public function actionPerfection()
	{
		$result = array('success'=>false);

		if (isset($_POST['id'])) 
		{
			$id = $_POST['id'];

			$user = User::model->findByPk($id);
			if($user != null)
			{
				$user->name = $_POST['name'];
				$user->area_id = $_POST['area'];
				$user->address = $_POST['address'];

				$user->save();
				result['success'] = true;

			}

		}

		$json = CJSON::encode($result);
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