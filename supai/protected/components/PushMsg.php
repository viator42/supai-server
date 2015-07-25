<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */

require_once 'vendor/autoload.php';

use JPush\Model as M;
use JPush\JPushClient;
use JPush\JPushLog;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class PushMsg
{
	var $app_key='d18febadd32dd84b1c3be46a';
	var $master_secret = '2cb6d42d425adff51ff31b5a';
	
	public function push($user_sn, $msg)
	{
		JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
		$client = new JPushClient($app_key, $master_secret);

		try {
		    $result = $client->push()
		        ->setPlatform(M\Platform('android'))
		        ->setAudience(M\alias(array($user_sn)))
		        ->setNotification(M\notification($msg))
		        ->printJSON()
		        ->send();
		    return $result;
		    
		} catch (Exception $e) {
		    return null;
		}
	}
}