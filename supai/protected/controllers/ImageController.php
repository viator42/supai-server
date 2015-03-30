<?php

class ImageController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionUpload()
	{
		$result = array('success'=>false);

		$tempfile = $_FILES['file']['tmp_name'];
		$filesize = $_FILES['file']['size'];
		$filetype = $_FILES['file']["type"];

		switch ($filetype) {
			case "image/gif":

			case "image/jpeg":

			case "image/pjpeg":
				if($filesize < 1024000)
				{
					$createFileName=uniqid(rand());
					$destPath = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$createFileName;

					if(move_uploaded_file($tempfile, $destPath))
					{ 
						$result['msg'] = "图片上传成功!";
						$result['success'] = true;
						$result['path'] = $_SERVER['SERVER_NAME'].'/upload/'.$createFileName;

					}
					else
					{
						$result['msg'] = "图片上传失败!";

					} 

				}
				else
				{
					$result['msg'] = "图片尺寸过大";

				}

				break;
			
			default:
				$result['msg'] = "文件类型不正确";
				break;
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