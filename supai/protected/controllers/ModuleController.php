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
                $module['price'] = $moduleObj->price;
                $module['status'] = $moduleObj->status;

                $current_time = time();
                if($current_time > $moduleObj->start_time && $current_time > $moduleObj->finish_time)
                {
                    $result[] = $module;

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
            $result['price'] = $moduleCategory->price;
            $result['code'] = $moduleCategory->code;

        }

        $json = str_replace("\\/", "/", CJSON::encode($result));
        echo $json;
    }

    //预定收费模块
    public function actionApply()
    {
        $result = array('success'=>false);

        $categoryId = $_POST['categoryId'];
        $userid = $_POST['userid'];
        $username = $_POST['username'];
        $tel = $_POST['tel'];
        $address = $_POST['address'];

        $module = Module::model()->find('user_id=:user_id and category_id=:category_id',
            array(':user_id'=>$userid, ':category_id'=>$categoryId));
        if($module != null)
        {
            $result['success'] = false;
            $result['msg'] = "您已提交申请,不能重复提交";

        }
        else
        {
            $module = new Module();

            $module->user_id = $userid;
            $module->category_id = $categoryId;
            $module->username = $username;
            $module->tel = $tel;
            $module->address = $address;
            $module->status = 3;

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