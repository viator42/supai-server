<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>速派网上超市－国内领先的在线零售平台</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="alternate icon" type="image/png" href="<? echo Yii::app()->request->baseUrl; ?>/images/webpage/favicon.png">
    <link rel="stylesheet" href="<? echo Yii::app()->request->baseUrl; ?>/assets/amazeui/css/amazeui.min.css"/>
    <link rel="stylesheet" href="<? echo Yii::app()->request->baseUrl; ?>/css/main.css"/>
</head>

<body>
<div class="am-g am-container">
    <ul class="am-nav am-nav-pills">
        <li><a href="#">注册登录</a></li>
        <li><a href="#">下单购物</a></li>
        <li><a href="#">开店与店铺管理</a></li>
        <li><a href="#">打印机的设置与使用</a></li>
        <li><a href="#">换机流程</a></li>
    </ul>
    <hr>
    <h3>注册登录</h3>
    输入手机号即可登录.<p>
        <img src="/images/webpage/tutorial/1-1.png" class="am-img-responsive"><p>
        如果您是第一次使用, 需要完善您的个人信息.<p>
        <img src="/images/webpage/tutorial/1-2.png" class="am-img-responsive"><p>
    <p>

        进入主界面<p>
        <img src="/images/webpage/tutorial/2-1.png" class="am-img-responsive"><p>

        周边用来搜索您所在位置周围的店铺,按照距离远近排列.



        为什么没有让我设置密码?<br>
        速派网上超市使用的是秘钥登录.注册之后即绑定您的手机,从此无需记忆复杂的密码.既方便使用又无需担心安全问题.他人即使冒用您的手机号也无法登录.如果您更换手机或者手机丢失,只需使用找回账号功能提交申请,我们会在核实您的身份后重新进行绑定.
    </p>
    <p>手机丢失或者换号怎么办?<br>
        此时可以用另一台手机登录您的账号,此时会出现找回账号的选项.点击找回账号并在表格中填入您的个人信息,完成后点击提交.我们会对您提供的信息进行人工审核.
        审核通过之后会以短信的形式通知您.至此换号完成,您可以用当前手机登录账号.</p>

    2.开店与店铺管理<p>
        点击主页面上的我要开店,打开新建店铺页面.填写名称,地址等信息.店铺位置默认获取您手机的当前位置,如果不是,可以点击位置按钮在地图上手动获取.<br>
        填写完成后点击提交.<p>

        创建完成后点击主屏幕上的"我的店铺"按钮进入店铺主页.<p>

        商品添加有扫码添加和拍照添加两种,我们推荐您在有条码的商品使用扫码添加,通过条形码可以在商品库中自动匹配商品的详细信息,省去手动添加的麻烦.<br>
        同时顾客也可以通过扫码找到您的商品.拍照添加适用于没有条码的自制商品.<p>

        我的页面->店铺设置可以修改店铺的基本信息.<p>

        客户列表<br>
        所有提交订单的用户都会显示在客户列表中,如果遭遇客户恶意购买可以点击"屏蔽"该用户.被屏蔽的用户将无法提交订单.<p>



        收藏的店铺会在顶部以标签页的形式显示,收藏的商品也会显示在主页上.

        主页

        打印机的设置与使用.<br>

        速派网上超市提供连接小票打印机功能.可以在客户提交订单的时候自动打印.方便送货人员.<br>

        1. 打印机接通电源并开启手机的蓝牙功能.<br>
        2. 打开我的页面->打印机,进入打印机控制页面.如图所示<br>
        <img src="/images/webpage/tutorial/printer2.png" class="am-img-responsive"><p>

        3. 点击打印机1,出现的端口设置框选择蓝牙.<br>
        <img src="/images/webpage/tutorial/printer3.png" class="am-img-responsive"><p>

        4. 点击配对的打印机设备,默认密码是0000.<br>
        <img src="/images/webpage/tutorial/printer3.png" class="am-img-responsive"><p>
        5. 如果设备列表出现蓝牙设备(如图所示),说明打印机已配对,点击右侧连接按钮进行连接.连接成功会听到打印机走纸的声音.<br>
        <img src="/images/webpage/tutorial/printer4.png" class="am-img-responsive"><p>
</div>

<!--[if (gte IE 9)|!(IE)]><!-->
<script src="<? echo Yii::app()->request->baseUrl; ?>/assets/amazeui/js/jquery.min.js"></script>
<script src="<? echo Yii::app()->request->baseUrl; ?>/assets/amazeui/js/amazeui.min.js"></script>

<script src="js/main.js"></script>
<!--<![endif]-->

</body>
</html>



