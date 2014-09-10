<?php

namespace Controller;

class SignIn extends \Controller\BaseController {

    static public $url = '/account/signin';

    static public function get() {
        return self::render('account/signin.html', get_defined_vars());
    }

    static public function post() {
        
    }
}
