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
	// 	ERROR_NONE=0;
	// ERROR_USERNAME_INVALID = 1;
	// ERROR_PASSWORD_INVALID = 2;
	// ERROR_UNKNOWN_IDENTITY = 100;       // the default
	public function actionLogin()
	{
		$result = array('success'=>false, 'errorCode'=>0);

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
            $result['sn'] = $user->sn;
            $result['passtype'] = $user->passtype;
            $result['clerk_of'] = $user->clerk_of;
            $result['status'] = $user->status;

            $result['success'] = true;

        }
        else
        {
            $result['success'] = false;
            $result['errorCode'] = $_identity->errorCode;

        }

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;

	}

    //验证密码
    public function actionValidatePassword()
    {
        $result = array('success'=>false, 'errorCode'=>UserIdentity::ERROR_PASSWORD_INVALID);

        $userid = $_POST['userid'];
        $password = $_POST['password'];

        $user = User::model()->findByPk($userid);
        if($user != null)
        {
            if($user->password == md5($password))
            {
                //登录成功
                $result['id'] = $user->id;
                $result['name'] = $user->name;
                $result['username'] = $user->username;
                $result['tel'] = $user->tel;
                $result['area'] = $user->area_id;
                $result['icon'] = $user->icon;
                $result['address'] = $user->address;
                $result['sn'] = $user->sn;
                $result['passtype'] = $user->passtype;
                $result['clerk_of'] = $user->clerk_of;
                $result['status'] = $user->status;

                $result['success'] = true;
            }

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
		if(!preg_match("/^1[35789]{1}[0-9]{9}$/",$tel))
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
			$user->icon = "/images/ic_user.png";

			$user->sn = uniqid();
			$user->passtype = 1;

			$user->save();

			//注册后回传值
			$result['id'] = $user->id;
		    $result['name'] = $user->name;
		    $result['username'] = $user->username;
		    $result['tel'] = $user->tel;
		    $result['area'] = $user->area_id;
		    $result['icon'] = $user->icon;
		    $result['address'] = $user->address;
		    $result['sn'] = $user->sn;
            $result['passtype'] = $user->passtype;
            $result['clerk_of'] = $user->clerk_of;
            $result['status'] = $user->status;

			$result['success'] = true;
			$result['msg'] = "注册成功";
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
    	echo $json;
	}

	// 修改用户信息
	public function actionUpdate()
	{
		$result = array('success'=>false);

		$id = $_POST['id'];

		$user = User::model()->findByPk($id);
		if($user != null)
		{
			$user->name = $_POST['name'];
			$user->address = $_POST['address'];
            if(isset($_POST['icon']) && $_POST['icon'] != null)
            {
                $user->icon = $_POST['icon'];

            }
			$user->area_id = $_POST['area'];

            $passtype = $_POST['passtype'];
            if($passtype == 2)
            {
                $user->passtype = $passtype;
                $user->password = $_POST['password'];
            }

			$user->save();

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
			$result['area'] = $user->area_id;
			$result['sn'] = $user->sn;
            $result['passtype'] = $user->passtype;
            $result['password'] = $user->password;
            $result['clerk_of'] = $user->clerk_of;
            $result['status'] = $user->status;

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

	//根据code获取省市信息
	public function actionAddress()
	{
		$result = array();
		$code = $_POST['code'];
		$data = "";

		$area = Area::model()->find('code=:code', array(':code' => $code));
		if($area != null)
		{
			if($area->p_code != 0)
			{
				$parea = Area::model()->find('code=:code', array(':code' => $area->p_code));
				$data = $data.$parea->name;
			}
			$data = $data.$area->name;
		}
		$result['data'] = $data;

		$json = CJSON::encode($result);
        echo $json;
	}

	//用户反馈
	public function actionRef()
	{
		$result = array('success'=>false);

		if (isset($_POST['userid']))
		{
			$userid = $_POST['userid'];

			$ref = new Ref();

			$ref->user_id = $userid;
			$ref->content = $_POST['content'];
			$ref->type = 1;
			$ref->create_time = time();
			$ref->parent_id = 0;

			$ref->save();
			$result['success'] = true;
		}

		$json = CJSON::encode($result);
        echo $json;
	}

	//用户反馈
	public function actionAppeal()
	{
		$result = array('success'=>false);

		$appeal = new UserAppeal();

		$appeal->old_tel = $_POST['oldTel'];
		$appeal->new_tel = $_POST['newTel'];
		$appeal->name = $_POST['name'];
		$appeal->address = $_POST['address'];
		$appeal->imie = $_POST['imie'];
		$appeal->area_id = 0;
		$appeal->type = 1;
		$appeal->create_time = time();

		$appeal->save();
		$result['success'] = true;

		$json = CJSON::encode($result);
        echo $json;
	}

    //查找用户信息根据tel
    public function actionFindByTel()
    {
        $result = array('success'=>false);

        $tel = $_POST['tel'];
        $user = User::model()->find('tel=:tel', array(':tel'=>$tel));

        if($user != null)
        {
            $result['id'] = $user->id;
            $result['name'] = $user->name;
            $result['username'] = $user->username;
            $result['tel'] = $user->tel;
            $result['icon'] = $user->icon;
            $result['sn'] = $user->sn;
            $result['passtype'] = $user->passtype;
            $result['clerk_of'] = $user->clerk_of;
            $result['status'] = $user->status;

            $result['success'] = true;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

	/*
	public function actionDestroy()
	{
		$result = array('success'=>false);
		$userid = $_POST['userid'];

		//删除user
		$user = User::model()->findByPk($userid);
		if($user != null)
		{
			$user->delete();

		}

		//删除store
		$store = Store::model()->find('user_id=:user_id', array(':user_id'=>$userid));
		if($store != null)
		{
			$store->delete();

			//删除product
			$product = Product::model()->find('store_id=:store_id', array(':store_id'=>$store->id));
			if($store != null)
			{
				$store->delete();

			}

			//删除收藏
			$storeCollectObjs = StoreCollect::model()->find('user_id=:user_id', array(':user_id'=>$userid));
			if($store != null)
			{
				$store->delete();

			}
			$productCollectObjs = ProductCollect::model()->findAll('user_id=:user_id', array(':user_id'=>$userid));
			foreach ($productCollectObjs as $productCollectObj)
			{
				$productCollectObj->delete();

			}

			//删除购物车
			$cartObjs = Cart::model()->findAll('user_id=:user_id', array(':user_id'=>$userid));
			foreach ($cartObjs as $cartObj)
			{
				$cartDetailObjs = CartDetail::model()->findAll('user_id=:user_id', array(''=>));
				foreach ($cartDetailObjs as $cartDetailObj)
				{
					$cartDetailObj->delete();
				}

				$cartObj->delete();

			}

			//删除订单
			//$orderObjs = Order::model()->findAll('user_id=:user_id', array(':user_id'=>$userid));

			$result['success'] = true;

		}

		$json = CJSON::encode($result);
        echo $json;
	}
	*/

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
