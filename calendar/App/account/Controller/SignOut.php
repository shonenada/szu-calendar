<?php

namespace Controller;

class SignOut extends \Controller\BaseController {

    static public $url = '/account/signout';

    static public function get() {
        return self::render('account/signout.html', get_defined_vars());
    }
}
