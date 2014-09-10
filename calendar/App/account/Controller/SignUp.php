<?php

namespace Controller;

class SignUp extends \Controller\BaseController {

    static public $url = '/account/signup';

    static public function get() {
        return self::render('account/signup.html', get_defined_vars());
    }
}
