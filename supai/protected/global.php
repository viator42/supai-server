<?php
/**
 * Created by PhpStorm.
 * User: viator42
 * Date: 15/10/23
 * Time: 下午1:02
 * 全局常量
 *
 */
class StaiticValues
{
    static $app_name = "速派网销平台";
    static $company_name = "济南速派信息技术有限公司";
    static $copyright = "© 2015 济南速派信息技术有限公司";
    static $tel = "13869173840";
    static $qq = "2724166624";
    static $address = "山东省桥区济安街9号楼2单元301";
    static $mail = "supai.jn@qq.com";


    static $versionName = "v4.0";
    static $versionCode = 10;

    //购物车
    static $CART_STATUS_NORMAL = 1;

    //店铺
    static $STORE_STATUS_OPEN = 1;
    static $STORE_STATUS_CLOSED = 2;
    static $STORE_STATUS_REMOVED = 3;

    static $STORE_PAY_AFTER_DISABLE = 1;
    static $STORE_PAY_AFTER_ENABLE = 2;

    static $STORE_PAY_METHOD_CASH = 1;
    static $STORE_PAY_METHOD_ALIPAY = 2;
    static $STORE_PAY_METHOD_DIRECT = 3;

    //关注者
    static $FOLLOWER_STATUS_FOLLOWED = 1;
    static $FOLLOWER_STATUS_BLOCKED = 2;

    static $FOLLOWER_SEARCH_TYPE_NAME = 1;
    static $FOLLOWER_SEARCH_TYPE_TEL = 2;


    //订单
    static $ORDER_TYPE_ACTIVE = 1;
    static $ORDER_TYPE_ARCHIVE = 2;

    static $ORDER_UNREAD = 1;
    static $ORDER_READED = 2;

    static $ORDER_RETURN_ACCEPT = 1;
    static $ORDER_RETURN_REJECT = 2;

    static $ORDER_PAID_N = 1;
    static $ORDER_PAID_Y = 2;

    static $ORDER_PAY_AFTER_DISABLE = 1;
    static $ORDER_PAY_AFTER_ENABLE = 2;

    static $ORDER_TYPE_ONLINE = 1;
    static $ORDER_TYPE_OFFLINE = 2;

    //订单状态:
    static $ORDER_STATUS_UNPAID = 1;         //未支付
    static $ORDER_STATUS_READY = 2;          //待发货
    static $ORDER_STATUS_DELIVERING = 3;    //已发货(发送通知)
    static $ORDER_STATUS_SUCCEED = 4;       //交易成功
    static $ORDER_STATUS_CLOSED = 5;        //交易关闭
    static $ORDER_STATUS_RETURN_APPLY = 6;  //申请退货

    //图片
    static $IMAGE_TYPE_PRODUCT = 1;
    static $IMAGE_TYPE_GOODS = 2;

    //收费模块
    static $MODULE_STATUS_ENABLE = 1;
    static $MODULE_STATUS_DISABLE = 2;
    static $MODULE_STATUS_APPLY = 3;

    //商品
    static $PRODUCT_STATUS_ENABLE = 1;
    static $PRODUCT_STATUS_DISABLE = 2;
    static $PRODUCT_STATUS_REMOVED = 0;

    //销售列表
    static $SALES_STATUS_ADDED = 1;

    static $SALES_ORDER_SEARCH_CLERK = 1;
    static $SALES_ORDER_SEARCH_STORE = 2;

    //用户
    static $USER_PASSTYPE_AUTO = 1;
    static $USER_PASSTYPE_INDEPENDENT = 2;

    //意见反馈
    static $REF_TYPE_SUGGESTION = 1;
    static $REF_TYPE_BUG = 2;

    //申诉
    static $APPEAL_TYPE_ACCOUNT = 1;    //找回账户

    //收藏
    static $FAVOURITE = 1;
    static $UNFAVOURITE = 0;


    static $LICENSE_NEW = 1;        //1 新建
    static $LICENSE_ENABLE = 2;      //2 已发放
    static $LICENSE_USED = 3;        //3 已激活
    static $LICENSE_DISABLED = 4;    //4 禁用

    //管理员imie
    static $MASTER_IMIE = '868291026540977';

}
