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

        $_identity = new UserIdentity($username, $password);

        $_identity->authenticate();

        if($_identity->errorCode===UserIdentity::ERROR_NONE)
        {
        	$user = $_identity->getUser();
            $result['id'] = $user->id;
            $result['name'] = $user->name;
            $result['username'] = $user->username;
            $result['tel'] = $user->tel;
            $result['area'] = $user->area_id;
            $result['icon'] = $user->icon;
            $result['address'] = $user->address;

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
		$result = array('success'=>false, 'msg'=>"注册失败");

		$tel = trim($_POST['tel']);
		$name = trim($_POST['name']);
		$imie = $_POST['password'];
		$password = md5($_POST['password']);
		$address = trim($_POST['address']);
		$area = $_POST['area'];

		//手机号码格式正则查询
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$tel))
		{
			$result['msg'] = "请输入正确的手机号";
		}
		//查询手机号是否已经注册
		elseif(User::model()->exists('tel=:tel', array(':tel'=>$tel)))
		{
			$result['msg'] = "此号码已经注册,请直接登录";
		}
		elseif(User::model()->exists('imie=:imie', array(':imie'=>$imie)))
		{
			$result['msg'] = "不允许一台手机注册多个账户";
		}
		else
		{
			//注册
			$user = new User();

			$user->imie = $imie;
			$user->username = $tel;
			$user->tel = $tel;
			$user->password = $password;
			$user->register_time = time();
			$user->lastlogin_time = time();
			$user->name = $name;
			$user->address = $address;
			$user->area_id = $area;
			 
			//默认头像
			$user->icon = 'http://'.$_SERVER['SERVER_NAME']."/images/ic_user.png";

			$user->save();

			//注册后回传值
			$result['id'] = $user->id;
            $result['name'] = $user->name;
            $result['username'] = $user->username;
            $result['tel'] = $user->tel;
            $result['area'] = $user->area_id;
            $result['icon'] = $user->icon;
            $result['address'] = $user->address;

			$result['success'] = true;
			$result['msg'] = "注册成功";
		}

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
			    $user->icon = $value;
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
			$result['longitude'] = $user->longitude;
			$result['latitude'] = $user->latitude;
			
			$result['success'] = true;

		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;	
	}

	//上传用户位置
	public function actionUploadLocation()
	{
		$result = array('success'=>false);

		$id = $_POST['userid'];
		$longitude = $_POST['longitude'];
		$latitude = $_POST['latitude'];

		$user = User::model()->findByPk($id);
		if($user != null)
		{
			$user->longitude = $longitude;
			$user->latitude = $latitude;
			$user->save();

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