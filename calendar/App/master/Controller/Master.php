<?php

namespace Controller;

class Master extends \Controller\BaseController {

    static public $url = '/';
    static public $allow = array(
        'GET' => array('Guest',),
    );

    static public function get() {
        self::signInRequired(self::urlFor('account.sign_in[get]'));
        self::redirectIfSignIned(self::urlFor('master.dashboard[get]'));
        return self::render('index.html', get_defined_vars());
    }
}
