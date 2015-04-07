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
	
	//查询商品详情
	public function actionDetail()
	{
		$result = array();
		$id = $_POST['id'];

		$productObj = Product::model->findByPk($id);
		
		$goodsObj = Goods::model()->findByPk($productObj->goods_id);
		$result['name'] = $goodsObj->name;
		$result['rccode'] = $goodsObj->rccode;
		$result['priceRecommend'] = $goodsObj->price;
		$result['description'] = $goodsObj->description;

		$result['price'] = $productObj->price;
		$result['count'] = $productObj->count;
		$result['addition'] = $productObj->description;

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