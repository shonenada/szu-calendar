<?php

namespace Extension;

use \Util\Helper;

class ViewFunction {

    static public $functions = null;

    static private $trans = null;
    static private $execTime = null;
    static private $dateDifference = null;
    static private $staticUrl = null;

    static public function init() {

        static::$trans = new \Twig_SimpleFunction('trans', function ($trans_id) {
            $app = \GlobalEnv::get('app');
            $lang = $app->getCookie('lang.code');
            if ($lang == null) {
                $lang = 'zh';
            }
            $message = require(APPROOT . 'translations/' . $lang . '/message.php');
            if (!array_key_exists($trans_id, $message)) {
                return $trans_id;
            }
            return $message[$trans_id];
        });

        static::$execTime = new \Twig_SimpleFunction('execTime', function ($precision, $untilTimestamp=null) {
            return Helper::execTime($precision, $untilTimestamp);
        });

        static::$dateDifference = new \Twig_SimpleFunction('dateDifference', function ($start, $end) {
            return $end->getTimestamp() - $start->getTimestamp();
        });

        static::$staticUrl = new \Twig_SimpleFunction('staticUrl', function ($url, $withUrl = false, $withUri = true, $appName = 'default') {
            $uri = '/' . ltrim($url, '/');
            if ($withUrl) {
                $req = \Slim\Slim::getInstance($appName)->request();
                if ($withUri) {
                    $uri .= $req->getRootUri();
                }
                $uri = $req->getUrl() . $uri;
            }
            return $uri;
        });

        static::$functions = array(
            static::$trans,
            static::$execTime,
            static::$dateDifference,
            static::$staticUrl,
        );
    }

}
ViewFunction::init();
