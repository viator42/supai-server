<?php

class StatisticsController extends Controller
{
//	public function actionIndex()
//	{
//		$this->render('index');
//	}

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

    //返回库存量少的商品列表
    public function actionProductLowInStore()
    {
        $result = array();

        $storeId = $_POST['storeId'];
        $page = $_POST['page'];
        $limit = (int)$_POST['limit'];	//每页的个数

        $store = Store::model()->findByPk($storeId);

        $productObjs = Product::model()->findAll('store_id=:store_id and status != :PRODUCT_STATUS_REMOVED and count <= :count order by count desc limit :offset, :limit',
            array(':store_id'=>$storeId, ':PRODUCT_STATUS_REMOVED'=>StaiticValues::$PRODUCT_STATUS_REMOVED, ':count'=>$store->storage_warning, ':offset'=>($page * $limit), ':limit'=>$limit));
        foreach ($productObjs as $productObj)
        {
            $product = array();

            $product['id'] = $productObj->id;
            $product['alias'] = $productObj->alias;
            $product['price'] = $productObj->price;
            $product['count'] = $productObj->count;
            $product['status'] = $productObj->status;

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

    //商品盘点
    public function actionProductParticulars()
    {
        $result = array();

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //统计信息
    public function actionInfo()
    {
        $result = array();

        $storeId = $_POST['storeId'];

        //商品总数 价格
        $productCount = 0;
        $productValueSum = 0;

        $productObjs = Product::model()->findAll('store_id=:store_id and status != :PRODUCT_STATUS_REMOVED',
            array(':store_id'=>$storeId, ':PRODUCT_STATUS_REMOVED'=>StaiticValues::$PRODUCT_STATUS_REMOVED));
        foreach ($productObjs as $productObj)
        {
            $productCount += 1;
            $value = $productObj->price * $productObj->count;
            $productValueSum += $value;
        }

        $result['productCount'] = $productCount;
        $result['productValueSum'] = $productValueSum;

        //营业额
        $turnoverToday = 0;
        $turnoverMonth = 0;
        $turnoverYear = 0;
        $unpaidSum = 0;

        $orderObjs = Order::model()->findAll('store_id=:store_id and status = :ORDER_STATUS_DELIVERING',
            array(':ORDER_STATUS_DELIVERING'=>StaiticValues::$ORDER_STATUS_DELIVERING, ':store_id'=>$storeId));
        foreach($orderObjs as $orderObj)
        {
            $createTime = $orderObj->create_time;

            if($this->ifInToday($createTime))
            {
                $turnoverToday += $orderObj->summary;
            }

            if($this->ifInThisMonth($createTime))
            {
                $turnoverMonth += $orderObj->summary;
            }

            if($this->ifInThisYear($createTime))
            {
                $turnoverYear += $orderObj->summary;
            }

            if($orderObj->pay_after = StaiticValues::$ORDER_PAY_AFTER_DISABLE and $orderObj->paid = StaiticValues::$ORDER_PAID_N)
            {
                $unpaidSum += $orderObj->summary;
            }

        }

        $result['turnoverToday'] = $turnoverToday;
        $result['turnoverMonth'] = $turnoverMonth;
        $result['turnoverYear'] = $turnoverYear;
        $result['unpaidSum'] = $unpaidSum;

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //商品明细列表
    public function actionParticulars()
    {
        $result = array();

        $storeId = $_POST['storeId'];
        $page = $_POST['page'];
        $limit = (int)$_POST['limit'];	//每页的个数

        $productObjs = Product::model()->findAll('store_id=:store_id and status != :PRODUCT_STATUS_REMOVED limit :offset, :limit',
            array(':store_id'=>$storeId, ':PRODUCT_STATUS_REMOVED'=>StaiticValues::$PRODUCT_STATUS_REMOVED, ':offset'=>($page * $limit), ':limit'=>$limit));
        foreach ($productObjs as $productObj)
        {
            $product = array();

            $product['id'] = $productObj->id;
            $product['alias'] = $productObj->alias;
            //商品图片
            $image = Image::model()->find('type=1 and type_id=:type_id', array(':type_id'=>$productObj->id));
            if($image != null)
            {
                $product['img'] = $image->url;
            }
            else
            {
                //加载默认图片
                $product['img'] = "/images/product_default.jpg";
            }
            $product['price'] = $productObj->price;
            $product['count'] = $productObj->count;

            $result[] = $product;
        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }
    //是否在当天之内
    public function ifInToday($time)
    {
        $now = time();
        $y = date('Y', $now);
        $m = date('m', $now);
        $d = date('t', $now);

        $firsttime = mktime(0,0,0,$m,$d,$y);
        $lasttime = mktime(23,59,59,$m,$d,$y);

        if($time > $firsttime && $time < $lasttime)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    //是否在本月之内
    public function ifInThisMonth($time)
    {
        $now = time();
        $y = date('Y', $now);
        $m = date('m', $now);
        $d = date('t', $now);

        $firsttime = mktime(0,0,0,$m,1,$y);
        $lasttime = mktime(23,59,59,$m,30,$y);

        if($time > $firsttime && $time < $lasttime)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //是否当年之内
    public function ifInThisYear($time)
    {
        $now = time();
        $y = date('Y', $now);
        $m = date('m', $now);
        $d = date('t', $now);

        $firsttime = mktime(0,0,0,1,1,$y);
        $lasttime = mktime(23,59,59,12,31,$y);

        if($time > $firsttime && $time < $lasttime)
        {
            return true;
        }
        else
        {
            return false;
        }

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