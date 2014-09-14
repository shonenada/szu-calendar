<?php

namespace Controller;

class StudentGroup extends \Controller\BaseController {

    static public $url = '/agenda/student-group';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        return self::render('agenda/student_group.html', get_defined_vars());
    }
}
