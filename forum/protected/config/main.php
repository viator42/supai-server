<?php
$config = __DIR__ . '/config.php';
if (!file_exists($config)) {
    header("Location: install.php");
    exit();
}
require $config;

return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'BIRDBBS',
    'theme' => 'mobile',
    'language' => 'zh_cn',
    'preload'=>array('log'),
    'defaultController' => 'home',
    
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.components.widget.*',
        'ext.mail.*',
        'application.vendor.sina.*',
    ),

    'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'admin',
            'ipFilters'=>array('127.0.0.1','::1'),
        ),
        'admin',
    ),

    // application components
    'components'=>array(
        
        'request' => array(
            'enableCsrfValidation'=>true,
            'enableCookieValidation'=>true,
            'csrfTokenName' => 'BIRDBBS_CSRF_TOKEN',
        ),

        'user'=>array(
            'allowAutoLogin'=>true,
            'loginUrl' => array('home/login'),
        ),
        // url rewirte
        // 'urlManager'=>array(
        //     'urlFormat'=>'path',
        //     'rules'=>array(
        //         // '<controller:\w+>/<id:\d+>'=>'<controller>/view',
        //         '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
        //         '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
        //     ),
        //     'showScriptName' => false,
        // ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'urlSuffix'=>'.html',
            'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
        'db'=>$CONF['db'],
        'cache' => array(
            'class' => 'system.caching.CFileCache',
        ),
        'errorHandler'=>array(
            'errorAction'=>'error/index',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
            ),
        ),
    ),
    'params' => $CONF['params'],
);