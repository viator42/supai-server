<?php

class StoreController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

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

	//创建店铺
	public function actionNew()
	{
		$result = array('success'=>false);

		$userid = $_POST['userid'];
		$name = $_POST['name'];
		$address = $_POST['address'];
		$description = $_POST['description'];
		$logo = $_POST['logo'];
		$longitude = $_POST['longitude'];
		$latitude = $_POST['latitude'];
		$area = $_POST['area'];


		//查询用户是否已有店铺
		$store = Store::model()->find('user_id=:user_id', array(':user_id'=>$userid));
		if($store == null)
		{
			$store = new Store();

			$store->user_id = $userid;
			$store->name = $name;
			$store->address = $address;
			$store->description = $description;
			$store->logo = $logo;
			$store->longitude = $longitude;
			$store->latitude = $latitude;
			$store->status = 1;
			$store->area_id = $area;
			$store->sn = uniqid();

            $store->storage_warning = 10;
			
			$store->save();

			$result['id'] = $store->id;
			$result['user_id'] = $store->user_id;
			$result['name'] = $store->name;
			$result['address'] = $store->address;
			$result['description'] = $store->description;
			$result['longitude'] = $store->longitude;
			$result['latitude'] = $store->latitude;
			$result['status'] = $store->status;
			$result['area_id'] = $store->area_id;
			$result['sn'] = $store->sn;

			$result['logo'] = $store->logo;

			$result['success'] = true;
		}

		$json = CJSON::encode($result);
        echo $json;

	}

	//查询用户的店铺信息
	public function actionInfo()
	{
		$result = array('success'=>false);

		$userid = $_POST['userid'];

		$storeObj = Store::model()->find('user_id=:user_id', array(':user_id'=>$userid));
		if($storeObj != null)
		{
			$data = array();
			$data['id'] = $storeObj->id;
			$data['name'] = $storeObj->name;
			$data['description'] = $storeObj->description;
			$data['address'] = $storeObj->address;
			$data['area_id'] = $storeObj->area_id;
			$data['logo'] = $storeObj->logo;
			$data['user_id'] = $storeObj->user_id;
			$data['favourite'] = 2;
			$data['longitude'] = $storeObj->longitude;
			$data['latitude'] = $storeObj->latitude;
			$data['status'] = $storeObj->status;
            $data['storageWarning'] = $storeObj->storage_warning;

			$result['data'] = $data;
			$result['success'] = true;

		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//查询用户的店铺信息
//	public function actionDetail()
//	{
//		$result = array('success'=>false);
//
//		$id = $_POST['id'];
//
//		$storeObj = Store::model()->findByPk($id);
//		if($storeObj != null)
//		{
//            $store = array();
//            $store['id'] = $storeObj->id;
//            $store['logo'] = $storeObj->logo;
//            $store['name'] = $storeObj->name;
//            $store['userId'] = $storeObj->user_id;
//            $store['address'] = $storeObj->address;
//            $store['description'] = $storeObj->description;
//            $store['longitude'] = $storeObj->longitude;
//            $store['latitude'] = $storeObj->latitude;
//            $store['favourite'] = 1;
//            $store['status'] = $storeObj->status;
//
//			$result['data'] = $store;
//			$result['success'] = true;
//
//		}
//
//		$json = str_replace("\\/", "/", CJSON::encode($result));
//        echo $json;
//	}


	//返回商店的所有商品列表
	public function actionProducts()
	{
		$result = array();

		$data = array();
		$products = array();

		$storeId = $_POST['id'];
		$page = $_POST['page'];
		$limit = (int)$_POST['limit'];	//每页的个数

		$productObjs = Product::model()->findAll('store_id=:store_id and status != 0 order by id desc limit :offset, :limit',
            array(':store_id'=>$storeId, ':offset'=>($page * $limit), ':limit'=>$limit));
		foreach ($productObjs as $productObj) 
		{
			$product = array();

			$product['goodsId'] = $productObj->goods_id;
			$product['id'] = $productObj->id;
			$product['alias'] = $productObj->alias;
			$product['additional'] = $productObj->description;
			$product['price'] = $productObj->price;
			$product['count'] = $productObj->count;
			$product['status'] = $productObj->status;
			$product['storeId'] = $productObj->store_id;

			if($productObj->goods_id != 0)
			{
				$goodsObj = Goods::model()->findByPk($productObj->goods_id);
				if($goodsObj != null)
				{

					$product['name'] = $goodsObj->name;
					$product['rccode'] = $goodsObj->barcode;
					$product['description'] = $goodsObj->description;
					$product['origin'] = $goodsObj->origin;
					$product['merchant'] = $goodsObj->merchant;
					$product['merchant_code'] = $goodsObj->merchant_code;

				}

			}
			$product['favourite'] = 0;
			$productCollectObj = ProductCollect::model()->find('product_id=:product_id', array(':product_id'=>$productObj->id));
			if($productCollectObj != null)
			{
				$product['favourite'] = 1;
			}

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

	// 返回一个地区内所有的店铺
	public function actionAround()
	{
		$result = array();

		$longitude = $_POST['longitude'];
		$latitude = $_POST['latitude'];
		$userid = $_POST['userid'];

		$user = User::model()->findByPk($userid);
		$storeObjs = Store::model()->findAll('area_id=:area_id and status=1', array(':area_id'=>$user->area_id));
		foreach ($storeObjs as $storeObj) 
		{
			$store = array();

			$store['id'] = $storeObj->id;
			$store['logo'] = $storeObj->logo;
			$store['name'] = $storeObj->name;
			$store['user_id'] = $storeObj->user_id;
			$store['area'] = $storeObj->area_id;
			$store['longitude'] = $storeObj->longitude;
			$store['latitude'] = $storeObj->latitude;
			$store['description'] = $storeObj->description;
			$store['address'] = $storeObj->address;

			//收藏状态
			$store['favourite'] = 0;
			$storeCollectObj = StoreCollect::model()->find('store_id=:store_id and user_id=:user_id', array(':store_id'=>$storeObj->id, ':user_id'=>$userid));
			if($storeCollectObj != null)
			{
				$store['favourite'] = 1;
			}

			//查询同一area内的所有店铺并忽略自己的店铺
			if($storeObj->status == 1 && $userid != $storeObj->user_id && $storeObj->area_id == $user->area_id)
			{
				$result[] = $store;
			}

			/*
			$longitudeMax = bcadd($longitude, $range, 6);
			$longitudeMin = bcsub($longitude, $range, 6);
			$latitudeMax = bcadd($latitude, $range, 6);
			$latitudeMin = bcsub($latitude, $range, 6);
			if(bccomp($longitudeMax, $storeObj->longitude, 6) == 1 && bccomp($longitudeMin, $storeObj->longitude, 6) == -1)
			{
				if(bccomp($latitudeMax, $storeObj->latitude, 6) == 1 && bccomp($latitudeMin, $storeObj->latitude, 6) == -1)
				{
					//
					if($storeObj->status == 1 && $userid != $storeObj->user_id)
					{
						$result[] = $store;
					}
					
				}

			}
			*/
			
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	// 修改店铺信息
	public function actionUpdate()
	{
		$result = array('success'=>false);

		$id = $_POST['id'];
		$name = $_POST['name'];
		$address = $_POST['address'];
		$description = $_POST['description'];
		$status = $_POST['status'];
		$longitude = $_POST['longitude'];
		$latitude = $_POST['latitude'];
        $storageWarning = $_POST['storage_warning'];
		
		$store = Store::model()->findByPk($id);
		if($store != null)
		{
			$store->name = $name;
			$store->address = $address;
			$store->description = $description;
			$store->status = $status;
			$store->longitude = $longitude;
			$store->latitude = $latitude;
            $store->storage_warning = $storageWarning;

			if(isset($_POST['logo']))
			{
				$store->logo = $_POST['logo'];

			}
			
			$store->save();
			$result['success'] = true;
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//收藏店铺
	public function actionAddFavourite()
	{
		$result = array('success'=>false);

		$userid = $_POST['userid'];
		$storeid = $_POST['storeid'];

		$storeCollect = StoreCollect::model()->find('user_id=:user_id and store_id=:store_id', array(':user_id'=>$userid, ':store_id'=>$storeid));
		if($storeCollect == null)
		{
			$storeCollect = new StoreCollect();

			$storeCollect->user_id = $userid;
			$storeCollect->store_id = $storeid;

			$storeCollect->save();	
			$result['success'] = true;
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

	//取消收藏店铺
	public function actionUnfavourite()
	{
		$result = array('success'=>false);

		$userid = $_POST['userid'];
		$storeid = $_POST['storeid'];

		$storeCollect = StoreCollect::model()->find('user_id=:user_id and store_id=:store_id', array(':user_id'=>$userid, ':store_id'=>$storeid));
		if($storeCollect != null)
		{
			$productCollectObjs = ProductCollect::model()->findAll('store_collect_id=:store_collect_id', array(':store_collect_id'=>$storeCollect->id));
			foreach ($productCollectObjs as $productCollectObj)
			{
				$productCollectObj->delete();

			}
			$storeCollect->delete();

			$result['success'] = true;
		}

		$json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
	}

    //店铺关注者
    public function actionFollower()
    {
        $result = array();

        $storeId = $_POST['storeid'];
        $page = $_POST['page'];
        $limit = (int)$_POST['limit'];	//每页的个数

        $followerObjs = Follower::model()->findAll('store_id=:store_id limit :offset, :limit',
            array(':store_id'=>$storeId, ':offset'=>($page * $limit), ':limit'=>$limit));
        foreach($followerObjs as $followerObj)
        {
            $follower = array();
            $customer = User::model()->findByPk($followerObj->customer_id);
            if($customer != null)
            {
                $follower['id'] = $followerObj->id;
                $follower['name'] = $customer->name;
                $follower['tel'] = $customer->tel;
                $follower['address'] = $customer->address;
                $follower['icon'] = $customer->icon;
                $follower['sn'] = $customer->sn;

                $follower['followTime'] = $followerObj->follow_time;
                $follower['status'] = $followerObj->status;

                $result[] = $follower;
            }
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //查找关注者
    public function actionSearchFollower()
    {
        $result = array();

        $storeId = $_POST['storeid'];
        $keyword = $_POST['keyword'];
        $type = $_POST['type'];     //查找类型 1:name   2:tel

        $userObjs = null;
        switch($type)
        {
            case 1:
                $userObjs = User::model()->findAll('`name` like :name',
                    array(':name'=>$keyword));

                break;

            case 2:
                $userObjs = User::model()->findAll('tel = :tel',
                    array(':tel'=>$keyword));

                break;

        }

        if($userObjs != null)
        {
            foreach($userObjs as $userObj)
            {
                $followerObj = Follower::model()->find('customer_id=:customer_id and store_id=:store_id', array(':customer_id'=>$userObj->id, ':store_id'=>$storeId));

                if($followerObj != null)
                {
                    $follower = array();

                    $follower['id'] = $followerObj->id;
                    $follower['name'] = $userObj->name;
                    $follower['tel'] = $userObj->tel;
                    $follower['address'] = $userObj->address;
                    $follower['icon'] = $userObj->icon;
                    $follower['sn'] = $userObj->sn;

                    $follower['followTime'] = $followerObj->follow_time;
                    $follower['status'] = $followerObj->status;

                    $result[] = $follower;
                }

            }

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //屏蔽用户
    public function actionBlockCustomer()
    {
        $result = array('success'=>false);

        $id = $_POST['id'];
        $status = $_POST['status'];

        $follower = Follower::model()->findByPk($id);

        if($follower != null)
        {
            switch($status)
            {
                case 1:
                    $status = 2;
                    break;
                case 2:
                    $status = 1;
                    break;
            }

            $follower->status = $status;
            $follower->save();

            $result['success'] = true;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //员工添加
    public function actionClerkRegister()
    {
        $result = array('success'=>false);

        $userid = $_POST['userid'];
        $storeId = $_POST['storeId'];

        $user = User::model()->findByPk($userid);
        if($user != null)
        {

            $user->clerk_of = $storeId;
            $user->save();
            $result['success'] = true;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //店铺员工列表
    public function actionClerks()
    {
        $result = array();

        $storeId = $_POST['storeId'];

        $userObjs = User::model()->findAll('clerk_of = :clerk_of', array(':clerk_of'=>$storeId));
        foreach($userObjs as $userObj)
        {
            $user = array();
            $user['id'] = $user->id;
            $user['sn'] = $user->sn;
            $user['name'] = $user->name;
            $user['username'] = $user->username;
            $user['icon'] = $user->icon;
            $user['tel'] = $user->tel;

            $result[] = $user;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //解雇员工
    public function actionClerkRemove()
    {
        $result = array('success'=>false);

        $userid = $_POST['userid'];

        $user = User::model()->findByPk($userid);
        if($user != null)
        {

            $user->clerk_of = 0;
            $user->save();
            $result['success'] = true;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //店铺销售
    public function actionSale()
    {
        $result = array('success'=>false);

        $userid = $_POST['userid'];
        $products = json_decode($_POST['userid']);



//        $user = User::model()->findByPk($userid);
//        if($user != null)
//        {
//
//            $user->clerk_of = 0;
//            $user->save();
//            $result['success'] = true;
//        }
//
//        $json = str_replace("\\/", "/", CJSON::encode($result));
//        echo $json;

        echo $products;

    }

    //查询员工是否有效
    public function actionClerkAvaliable()
    {
        $result = array('success'=>false);

        $userid = $_POST['userid'];

        $user = User::model()->findByPk($userid);
        if($user != null)
        {
            if($user->clerk_of != 0)
            {
                $result['success'] = true;

            }

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

	/*
	public function actionStoreProducts()
	{
		$result = array('success'=>false);

		$products = array();

		$storeId = $_POST['id'];

		$productObjs = Product::model->findAll('store_id=:store_id', array(':store_id'=>$storeId));

		foreach ($productObjs as $productObj) 
		{
			$product = array();
			$product['id'] = $productObj->id;
			$product['price'] = $productObj->price;

			$goodsObj = Goods::model->findByPk($productObj->goods_id);
			$product['name'] = $productObj->price;
			$product['rccode'] = $productObj->rccode;

			$products[] = $product;

		}

		$result['data'] = $products;

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