<?php

class ModuleController extends Controller
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

    //返回用户购买的模块
    public function actionList()
    {
        $result = array();

        $userid = $_POST['userid'];

        $moduleObjs = Module::model()->findAll('user_id=:user_id and status = 1', array(':user_id'=>$userid));
        foreach ($moduleObjs as $moduleObj)
        {
            $moduleCategoryObj = ModuleCategory::model()->findByPk($moduleObj->category_id);
            if($moduleCategoryObj != null)
            {
                $module = array();

                $module['id'] = $moduleObj->id;
                $module['code'] = $moduleCategoryObj->code;
                $module['name'] = $moduleCategoryObj->name;
                $module['start_time'] = $moduleObj->start_time;
                $module['finish_time'] = $moduleObj->finish_time;

                $current_time = time();
                if($current_time > $moduleObj->start_time && $current_time > $moduleObj->finish_time)
                {
                    $result[] = $module;

                }

            }

        }
//        $orderObj = Order::model()->findByPk($orderId);
//        if($orderObj != null)
//        {
//            $orderObj->status = 5;
//            $orderObj->save();
//
//            $merchant = User::model()->findByPk($orderObj->merchant_id);
//            $customer = User::model()->findByPk($orderObj->customer_id);
//            if($merchant != null && $customer != null)
//            {
//                //发送推送通知
//                $result['msg'] = $this->sendMsg(array($merchant->sn, $customer->sn), "您好,编号 ".$orderObj->sn." 的订单已被取消.");
//
//            }
//
//            $result['success'] = true;
//
//        }


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