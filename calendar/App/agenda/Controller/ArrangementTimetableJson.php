<?php

namespace Controller;

class ArrangementTimetableJson extends \Controller\BaseController {

    static public $url = '/arrangement-timetable';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        $start = self::$request->get('start');
        $end = self::$request->get('end');
        $arrangement = self::$currentUser->calendars;
        $calendars = \Model\Calendar::getTeacherWorkArrangement($arrangement->toArray(), $start, $end, array('convert' => true));
        return json_encode($calendars);
    }
}
