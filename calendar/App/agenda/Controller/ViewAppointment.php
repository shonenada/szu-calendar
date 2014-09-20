<?php

namespace Controller;

class ViewAppointment extends \Controller\BaseController {

    static public $url = '/agenda/view-reservation';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        $appointments = \Model\Calendar::getTeacherAppointment(self::$currentUser);
        return self::render('agenda/view_appointments.html', get_defined_vars());
    }
}
