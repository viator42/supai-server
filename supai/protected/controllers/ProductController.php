<?php

//商品控制器
class ProductController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	//最近购买的商品列表
	public function actionRecent()
	{
		$result = array();

		$id = $_POST['userid'];
		$recentBoughts = RecentBought::model()->findAll('userid = :userid', array(':userid'=>$id));
		foreach($recentBoughts as $recentBought) 
		{
			$lastBoughtProduct = array();

			$product = SaleProduct::model()->findByPk($recentBought->product_id);

			$lastBoughtProduct['id'] = $recentBought->product_id;
			$lastBoughtProduct['name'] = $product->name;

			$result[] = $lastBoughtProduct;
		}

		$json = CJSON::encode($result);
        echo $json;
	}
	
	//返回商家所有商品.
	

	

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