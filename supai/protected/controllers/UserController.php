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

		$username = $_POST['tel'];
		$password = $_POST['password'];

		// $model = User::model()->find('tel = :tel and password = :password', array(':tel'=>$tel, ':password'=>$password));
		// $_identity = new UserIdentity($username, $password);
  //       $_identity->authenticate();

		// if($model != null)
		// {
		// 	$model->lastlogin_time = time();
		// 	$model->save();
			
		// 	$result['data'] = $model;

		// 	$result['success'] = true;
		// }

        $_identity = new UserIdentity($username, $password);

        $_identity->authenticate();

        if($_identity->errorCode===UserIdentity::ERROR_NONE)
        {
            $result['data'] = $_identity->getUser();
            $result['success'] = true;

        }
        else
        {
            $result['success'] = false;

        }
		
		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;

	}

	//注册
	public function actionRegister()
	{
		$result = array('success'=>false);

		$tel = $_POST['tel'];
		$name = $_POST['name'];
		$password = md5($_POST['password']);
		$address = $_POST['address'];
		$area = $_POST['area'];

		$model = new User();

		$model->username = $tel;
		$model->tel = $tel;
		$model->password = $password;
		$model->register_time = time();
		$model->lastlogin_time = time();
		$model->name = $name;
		$model->address = $address;
		$model->area_id = $area;
		 
		//默认头像
		$model->icon = "http://192.168.1.10/images/icon.jpg";

		$model->save();

		$result['data'] = $model;
		$result['success'] = true;

		$json = CJSON::encode($result);
        echo $json;

	}
	
	// 修改用户信息
	public function actionUpdate()
	{
		$result = array('success'=>false);

		$id = $_POST['id'];
		$key = $_POST['key'];
		$value = $_POST['value'];

		$user = User::model()->findByPk($id);
		if($user != null)
		{
			switch ($key) {
			case "name":
			    $user->name = $value;
			    break;
			case "icon":
			    $user->logo = $value;
			    break;
			case "address":
			    $user->address = $value;
			    break;
			}
			
			$user->save();
			$result['value'] = $value;
			$result['success'] = true;
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//读取设置信息
	public function actionLoadSettings()
	{
		$result = array('success'=>false);

		$id = $_POST['userid'];

		$user = User::model()->findByPk($id);
		if($user != null)
		{
			$result['username'] = $user->username;
			$result['id'] = $user->id;
			$result['tel'] = $user->tel;
			$result['icon'] = $user->icon;
			$result['name'] = $user->name;
			$result['address'] = $user->address;

			$result['success'] = true;

		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
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