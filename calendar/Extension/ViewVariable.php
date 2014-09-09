<?php

use \Slim;
namespace Extension;

class ViewVariable {

    static public $vars = null;

    static public function init() {
        static::$vars = array(
            'siteTitle' => 'SZU Calendar',
            'langCode' => \Slim\Slim::getInstance()->getCookie('lang.code'),
        );
    }

}
ViewVariable::init();
