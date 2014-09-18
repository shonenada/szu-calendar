<?php

namespace Controller;

class ArrangementTimetableJson extends \Controller\BaseController {

    static public $url = '/arrangement-timetable';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        self::requiredRole('Teacher');
        $start = self::$request->get('start');
        $end = self::$request->get('end');
        $arrangement = self::$currentUser->calendars;
        $calendars = \Model\Calendar::getArrangementJson($arrangement->toArray(), $start, $end);
        return json_encode($calendars);
    }
}
