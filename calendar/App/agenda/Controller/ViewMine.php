<?php

namespace Controller;

class ViewMine extends \Controller\BaseController {

    static public $url = '/agenda/view';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        return self::render('agenda/view_mine.html', get_defined_vars());
    }
}
