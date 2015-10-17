<?php

/**
 * Class OrderCheckCommand
 * 检查订单状态任务,将过期未付款的任务状态
 * 用法
 * 添加到 crontab -e
 * 命令:  0 2 * * * /var/www/supai-server/supai/protected/yiic OrderCheck >> /var/www/supai-server/supai/protected/log/OrderCheck.log
 */

class OrderCheckCommand extends CConsoleCommand
{
    public function run($args) {
        //所要执行的任务，如数据符合某条件更新，删除，修改
        echo "Order outdate check";
    }
}