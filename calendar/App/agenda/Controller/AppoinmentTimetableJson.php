<?php

namespace Controller;

class AppoinmentTimetableJson extends \Controller\BaseController {

    static public $url = '/appoinment-timetable';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate'),
    );

    static public function get() {
        $start = self::$request->get('start');
        $end = self::$request->get('end');
        $calendars = \Model\Calendar::getStudentVisiableCalendar(self::$currentUser, $start, $end);
        return json_encode($calendars);
    }
}
