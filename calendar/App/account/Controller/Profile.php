<?php

namespace Controller;

class Profile extends \Controller\BaseController {

    static public $url = '/account/profile';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate', 'Teacher', 'Administrator'),
    );

    static public function get() {
        return self::render('account/profile.html', get_defined_vars());
    }
}
