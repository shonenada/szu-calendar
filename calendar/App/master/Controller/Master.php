<?php

namespace Controller;

class Master extends \Controller\BaseController {

    static public $url = '/';
    static public $allow = array(
        'GET' => array('EveryOne',),
    );

    static public function get() {
        return self::render('index.html', get_defined_vars());
    }
}
