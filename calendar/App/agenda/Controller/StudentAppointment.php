<?php

namespace Controller;

class StudentAppointment extends \Controller\BaseController {

    static public $url = '/agenda/student-appointment';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate'),
        'POST' => array('Undergraduate', 'Graduate'),
    );

    static public function get() {
        return self::render('agenda/student_appointment.html', get_defined_vars());
    }

    static public function post() {
        if (self::$currentUser->appointments->count() > \Constant\Agenda::MAX_APPOINTMENT_TIMES) {
            return json_encode(array(
                'success' => false,
                'message' => '您超过了预约限度',
            ));
        }

        $cid = self::$request->post('cid');
        $calendar = \Model\Calendar::find($cid);
        if ($calendar == null || !$calendar->canVisitedByAccount(self::$currentUser)) {
            return json_encode(array(
                'success' => false,
                'message' => '无法预约该时段',
            ));
        }

        # 检验时间是否被预约了

        $inputStartString = self::$request->post('start');
        $inputStart = \DateTime::createFromFormat('Y-m-d H:i:s', $inputStartString);
        $inputEndString = self::$request->post('end');
        $inputEnd = \DateTime::createFromFormat('Y-m-d H:i:s', $inputEndString);

        $newClds = self::rebuildCalendar($calendar, $inputStart, $inputEnd);

        $remark = self::$request->post('remark');
        $appointment = new \Model\Appointment();
        $appointment->account = self::$currentUser;
        $appointment->calendar = $newClds;
        $appointment->remark = $remark;
        $appointment->save();
        \Model\Appointment::flush();

        return json_encode(array(
            'success' => true,
        ));
    }

    static private function rebuildCalendar($calendar, $inputStart, $inputEnd) {
        $cStart = $calendar->startTime;
        $cEnd = $calendar->endTime;
        $visibleGroups = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($calendar->visibleGroups as $one) {
            $visibleGroups[] = $one;
        }
        if ($cStart < $inputStart) {
            $before = clone $calendar;
            $before->visibleGroups = $visibleGroups;
            $before->endTime = $inputStart;
            $before->save();
        }
        if ($cEnd > $inputEnd) {
            $after = clone $calendar;
            $after->visibleGroups = $visibleGroups;
            $after->startTime = $inputEnd;
            $after->save();
        }
        $currentCalendar = clone $calendar;
        $currentCalendar->startTime = $inputStart;
        $currentCalendar->endTime = $inputEnd;
        $currentCalendar->visibleGroups = $visibleGroups;
        $currentCalendar->save();
        $calendar->delete();
        \Model\Calendar::flush();
        return $currentCalendar;
    }

}
