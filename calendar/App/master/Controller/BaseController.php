<?php

namespace Controller;

class BaseController {

    const KEY_OF_MESSAGE = 'flashMsg';

    static protected $app;
    static protected $request;
    static protected $currentUser;
    static private $injectedArgs = array();

    static public function before() {}

    static public function after() {}

    static private function prepare() {
        $app = self::$app = \Slim\Slim::getInstance();
        $request = self::$request = $app->request;
        $user = self::$currentUser = self::getCurrentUser();
        $injectedArgs['user'] = $user;
    }

    static public function getCurrentUser() {
        if (!isset(self::$app)) 
            self::$app = \Slim\Slim::getInstance();
        $uid = self::$app->getCookie("userid");
        $user = \Model\Account::find($uid);
        return $user;
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

    static protected function flash($message, $type = 'info') {
        self::$app->flashArr(self::KEY_OF_MESSAGE, array($message, $type));
    }

    static protected function render($path, $args = array(), $status = null) {
        $args = array_merge($args, self::$injectedArgs);
        return self::$app->render($path, $args, $status);
    }

    static public function redirect($url, $status = 302) {
        self::$app->flashRedirectKeep();
        return self::$app->redirect($url, $status);
    }

    static public function redirectIfSignIned($url, $status = 302) {
        if (self::$currentUser)
            return self::redirect($url, $status);
    }

    static public function signInRequired($url, $status = 302) {
        if (!self::$currentUser)
            return self::redirect($url, $status);
    }

    static public function urlFor($name, $params = array()) {
        return self::$app->urlFor($name, $params);
    }

}