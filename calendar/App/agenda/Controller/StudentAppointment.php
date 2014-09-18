<?php

namespace Controller;

class StudentAppointment extends \Controller\BaseController {

    static public $url = '/agenda/student-appoinment';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate'),
    );

    static public function get() {
        return self::render('agenda/student_appointment.html', get_defined_vars());
    }

}
