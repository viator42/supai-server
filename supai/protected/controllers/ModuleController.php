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
    /**
     *
     */
    public function actionList()
    {
        $result = array();
        $current_time = time();
        $userid = $_POST['userid'];

        $moduleObjs = Module::model()->findAll('user_id=:user_id and status = 1', array(':user_id'=>$userid));
        foreach ($moduleObjs as $moduleObj)
        {
            $moduleBundleObj = ModuleBundle::model()->findByPk($moduleObj->bundle_id);
            if($moduleBundleObj != null)
            {
                //查询bundle中的模块信息
                $categories = array();
                $moduleBundleCategoryObjs = ModuleBundleCategory::model()->findAll('bundle_id=:bundle_id',array(':bundle_id'=>$moduleBundleObj->id));
                foreach($moduleBundleCategoryObjs as $moduleBundleCategoryObj)
                {
                    $moduleCategory = ModuleCategory::model()->findByPk($moduleBundleCategoryObj->category_id);
                    if($moduleCategory != null)
                    {
                        if($current_time >= $moduleObj->start_time && $current_time <= $moduleObj->finish_time)
                        {
                            $result[] = $moduleCategory;

                        }

                    }

                }

            }

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //返回模块详情
    public function actionDetail()
    {
        $result = array();

        $id = $_POST['id'];

        $moduleCategory = ModuleCategory::model()->findByPk($id);
        if($moduleCategory != null)
        {
            $result['id'] = $moduleCategory->id;
            $result['name'] = $moduleCategory->name;
            $result['description'] = $moduleCategory->description;
            $result['code'] = $moduleCategory->code;

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //预定收费模块
    public function actionApply()
    {
        $result = array('success'=>false);

        $userid = $_POST['userid'];
        $username = $_POST['username'];
        $tel = $_POST['tel'];
        $address = $_POST['address'];
        $buldleId = $_POST['bundle'];

        $module = Module::model()->find('user_id=:user_id and bundle_id=:bundle_id',
            array(':user_id'=>$userid, ':bundle_id'=>$buldleId));
        if($module != null)
        {
            $result['success'] = false;
            $result['msg'] = "您已提交申请,不能重复提交";

        }
        else
        {
            $module = new Module();
//
            $module->user_id = $userid;
            $module->bundle_id = $buldleId;
            $module->username = $username;
            $module->tel = $tel;
            $module->address = $address;
            $module->status = 3;

            //如果有cdkey直接开通
            if(isset($_POST['license']))
            {
                $key = $_POST['license'];

                $license = License::model()->find('key=:key', array(':key'=>$key));
                if($license != null)
                {
                    if($license->status == 2 && $license->userid == 0)
                    {
                        $license->userid = $userid;
                        $license->active_time = time();

                        //直接开通
                        $moduleBundle = ModuleBundle::model()->findByPk($module->bundle_id);
                        $module->status = 1;
                        $module->start_time = time();
                        $module->finish_time = $module->start_time + $moduleBundle->time_range;

                    }

                }

            }

            $module->save();

            $result['success'] = true;
        }

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