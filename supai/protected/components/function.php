<?php
/**
 * Created by PhpStorm.
 * User: viator42
 * Date: 15/8/6
 * Time: 上午11:13
 */

function sendMsg($userSnArray, $msg, $extras)
{
    $app_key = "d18febadd32dd84b1c3be46a";
    $master_secret = "2cb6d42d425adff51ff31b5a";

    $data = array();

    $data["platform"] = array("android");
    $data["audience"] = array("alias"=>$userSnArray);
    $data["notification"] = array("alert"=>$msg, "android"=>array("extras"=>$extras));

    $json_string = CJSON::encode($data);
    $auth_info = base64_encode($app_key.':'.$master_secret);

    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.jpush.cn/v3/push");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.$auth_info
        )
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);

    return  curl_exec($ch);
}