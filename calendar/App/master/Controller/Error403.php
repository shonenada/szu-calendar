<?php

namespace Controller;

class Error403 extends \Controller\BaseController {

    static public $url = '/errors/403';
    static public $name = 'error-403';
    static public $allow = array(
        'GET' => array('EveryOne',),
    );

    static public function get() {
        return self::render('errors/403.html', get_defined_vars(), 403);
    }
}
