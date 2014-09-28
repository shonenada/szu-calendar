<?php

namespace Controller;

class WorkArrangementList extends \Controller\BaseController {

    static public $url = '/agenda/work-arrangment-list';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        // $showExpire = (self::$request->get('show_expire') == '√');
        $now = new \DateTime();
        $showExpire = true;
        $arrangement = self::$currentUser->calendars;
        $calendars = \Model\Calendar::getTeacherWorkArrangement($arrangement->toArray(), null, null, array('convert' => false));
        if (!$showExpire) {
            $calendars = array_filter($calendars, function($one) use($now) {
                return $one->startTime->getTimestamp() >= $now->getTimestamp();
            });
        }
        usort($calendars, function($one, $two) {
            if ($one->startTime->getTimestamp() == $two->startTime->getTimestamp()) {
                return 0;
            }
            return ($one->startTime->getTimestamp() > $two->startTime->getTimestamp());

        });
        return self::render('agenda/work_arrangement_list.html', get_defined_vars());
    }
}