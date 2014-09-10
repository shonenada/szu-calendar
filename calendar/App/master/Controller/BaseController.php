<?php

namespace Controller;

class BaseController {

    static protected $app;

    static public function before() {}

    static public function after() {}

    static private function prepare() {
        self::$app = \Slim\Slim::getInstance();
    }

    static public function _get() {
        self::prepare();
        self::before();
        $return = call_user_func_array(array(get_called_class(), 'get'), func_get_args()); 
        self::after();
        return $return;
    }

    static public function _post() {
        self::prepare();
        self::before();
        $return = call_user_func_array(array(get_called_class(), 'post'), func_get_args()); 
        self::after();
        return $return;
    }

    static public function _put() {
        self::prepare();
        self::before();
        $return = call_user_func_array(array(get_called_class(), 'put'), func_get_args()); 
        self::after();
        return $return;
    }

    static public function _delete() {
        self::prepare();
        self::before();
        $return = call_user_func_array(array(get_called_class(), 'delete'), func_get_args()); 
        self::after();
        return $return;
    }

    static protected function render($path, $args = array(), $status = null) {
        return self::$app->render($path, $args, $status);
    }

    static public function redirect($url, $status = 302) {
        return self::$app->redirect($url, $status);
    }

    static public function urlFor($name, $params = array()) {
        return self::$app->urlFor($name, $params);
    }

}