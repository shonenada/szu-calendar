<?php

/**
 * 定义系统入口工厂函数
 * @author shonenada
 *
 */

$installedApps = array (
    'master',
    'account',
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

    // 读取用户自定义的配置
    foreach ($configFiles as $path) {
        if (is_file($path)) {
            $app->config(require_once($path));
        }
    }

    \Extension\View::setup($app);
    \Extension\Middleware::setup($app);

    global $installedApps;
    $app->installApps($installedApps);

    return $app;

}
