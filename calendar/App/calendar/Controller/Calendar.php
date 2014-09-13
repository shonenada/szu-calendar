<?php

namespace Controller;

class Calendar extends \Controller\BaseController {

    static public $url = '/calendar';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate', 'Teacher', 'Administrator'),
    );

    static public function get() {
        return self::render('calendar/index.html', get_defined_vars());
    }
}
