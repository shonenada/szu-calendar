<?php

/**
 * 定义系统入口工厂函数
 * @author shonenada
 *
 */

$installedApps = array (
    'master',
    'account',
    'agenda',
    'calendar',
);


// 系统入口工厂函数
function createApp ($configFiles=array()) {

    if(!is_array($configFiles))
        exit('Config files are not array.');

    // 初始化 app
    $app = new Slimx();
    // 载入配置
    $config = require_once(APPROOT . 'config/common.php');
    $app->config($config);

    date_default_timezone_set('Asia/Shanghai');

    // 读取用户自定义的配置
    foreach ($configFiles as $path) {
        if (is_file($path)) {
            $app->config(require_once($path));
        }
    }

    global $installedApps;
    $app->installApps($installedApps);

    \Extension\Auth::setup($app);
    \Extension\View::setup($app);
    \Extension\Middleware::setup($app);

    return $app;

}
