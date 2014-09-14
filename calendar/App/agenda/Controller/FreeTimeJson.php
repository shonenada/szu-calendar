<?php

namespace Controller;

class FreeTimeJson extends \Controller\BaseController {

    static public $url = '/free-time-json';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        self::requiredRole('Teacher');
        $start = self::$request->get('start');
        $end = self::$request->get('end');
        $freeTime = self::$currentUser->calendars;
        $calendars = \Model\Calendar::convertForFullCalendar($freeTime->toArray(), $start, $end, \Model\Calendar::TYPE_FREE);
        return json_encode($calendars);
    }
}
