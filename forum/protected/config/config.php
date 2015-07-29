<?php
$CONF = array();
$CONF['db'] = array(
    'connectionString' => 'mysql:host=127.0.0.1;dbname=supai-forum;port=8889',
    'emulatePrepare' => true,
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
    'tablePrefix' => 'supai',
    'schemaCachingDuration' => 3600
);
$CONF['params'] = array(
    'adminEmail'=>'supai.jn@qq.com',
    'mail' => array(
    'noreply' => 'supai.jn@qq.com',
    'smtp' => 'smtp.exmail.qq.com',
    'password' => 'spsddc2015',
));
