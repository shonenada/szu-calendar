<?php

namespace Controller;

class Dashboard extends \Controller\BaseController {

    static public $url = '/dashboard';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate', 'Teacher', 'Administrator'),
    );

    static public function get() {
        return self::render('dashboard/index.html', get_defined_vars());
    }
}
