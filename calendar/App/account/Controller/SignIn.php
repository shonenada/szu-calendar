<?php

namespace Controller;
use \Model\Account;

class SignIn extends \Controller\BaseController {

    static public $url = '/account/signin';
    static public $allow = array(
        'GET' => array('EveryOne',),
    );

    static public function get() {
        return self::render('account/signin.html', get_defined_vars());
    }

    static public function post() {
        
    }
}
