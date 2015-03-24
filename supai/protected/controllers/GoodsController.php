<?php

class GoodsController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	//商品详情	
	public function actionDetail()
	{
		$result = array('success'=>false);

		$id = $_GET['id'];
		$goods = Goods::model()->findByPk($id);
		if($goods != null)
		{
			$result['success'] = true;

		}


		$json = CJSON::encode($result);
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