<?php

namespace Controller;

class AppointmentTimetableJson extends \Controller\BaseController {

    static public $url = '/appointment-timetable';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate'),
    );

    static public function get() {
        $start = self::$request->get('start');
        $end = self::$request->get('end');
        $userCanAppoint = self::$currentUser->appointments->count() < \Constant\Agenda::MAX_APPOINTMENT_TIMES;
        $userAppointmentsArray = self::$currentUser->appointments->toArray();
        $calendars = \Model\Calendar::getStudentVisiableCalendar(self::$currentUser, $start, $end);
        $calendars = \Model\Calendar::splitCalendarsTime($calendars, \Constant\Agenda::MIN_MINUTES);
        $calendars = \Model\Calendar::convertForFullCalendar($calendars, array(), function($insert, $one) use ($userCanAppoint, $userAppointmentsArray) {
            $insert['mine'] = false;
            $insert['canAppoint'] = false;
            if (!$userCanAppoint) {
                $insert['color'] = '#b0b0b0';
                $insert['borderColor'] = '#737373';
            } else if ($one->appointment != null) {
                $insert['color'] = '#b0b0b0';
                $insert['borderColor'] = '#737373';
            } else {
                $insert['canAppoint'] = true;
            }
            if (in_array($one->appointment, $userAppointmentsArray)) {
                $insert['mine'] = true;
                $insert['color'] = '#1ba15f';
                $insert['borderColor'] = '#1d8e7b';
            }
            $insert['teacherName'] = $one->account->name;
            return $insert;
        });
        return json_encode($calendars);
    }
}
