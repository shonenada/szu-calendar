<?php

/**
 * 定义系统入口工厂函数
 * @author shonenada
 *
 */


// 系统入口工厂函数
function createApp ($configFiles=array()) {

    if(!is_array($configFiles))
        exit('Config files are not array.');

    // 初始化 app
    $app = new \Slim\Slim();
    $app->get('/version', function () {
        echo 'szu-calendar 0.1';
    });
    return $app;

}
