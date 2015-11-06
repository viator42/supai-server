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
    <link rel="alternate icon" type="image/png" href="/images/webpage/favicon.png">
    <link rel="stylesheet" href="/assets/amazeui/css/amazeui.min.css"/>
    <link rel="stylesheet" href="/css/main.css"/>
</head>

<body>
    <header class="am-topbar am-topbar-fixed-top">
        <div class="am-container">
            <h1 class="am-topbar-brand">
                <img src="/images/webpage/logo.jpg">速派网销平台
            </h1>

            <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-secondary am-show-sm-only"
                    data-am-collapse="{target: '#collapse-head'}"><span class="am-sr-only">导航切换</span> <span
                    class="am-icon-bars"></span></button>

            <div class="am-collapse am-topbar-collapse" id="collapse-head">
                <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right">
                    <li><a href="/site/tutorial">如何使用</a></li>
                    <li><a href="http://forum.supai.in/">论坛</a></li>

                </ul>

            </div>
        </div>
    </header>

<!--    <header class="am-topbar am-topbar-fixed-top">-->
<!--        <div class="am-container">-->
<!--            <h1 class="am-topbar-brand">-->
<!--                <a href="#">速派网上超市</a>-->
<!--            </h1>-->
<!--            <div class="am-topbar-right">-->
<!--                <a href="http://forum.supai.in/" class="am-btn am-btn-primary am-topbar-btn am-btn-sm">论坛</a>-->
<!--            </div>-->
<!--        </div>-->
<!--    </header>-->

	<?php echo $content; ?>

    <footer class="footer">
        <p><? echo StaiticValues::$copyright; ?>
            <br>鲁ICP备15023468号<br>Power by Yii Framework <? echo Yii::getVersion();?></p>
    </footer>

    <!--[if (gte IE 9)|!(IE)]><!-->
    <script src="/assets/amazeui/js/jquery.min.js"></script>
    <script src="/assets/amazeui/js/amazeui.min.js"></script>

    <script src="js/main.js"></script>
    <!--<![endif]-->

</body>
</html>
