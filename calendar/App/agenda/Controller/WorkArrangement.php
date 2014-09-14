<?php

namespace Controller;

class WorkArrangement extends \Controller\BaseController {

    static public $url = '/agenda/work-arrangment';
    static public $allow = array(
        'GET' => array('Teacher'),
        'POST' => array('Teacher'),
    );

    static public function get() {
        return self::render('agenda/work_arrangement.html', get_defined_vars());
    }

    static public function post() {
        self::requiredRole('Teacher');
        $post = self::$request->post();

        if (!isset($post['freeTime']) && !isset($post['deleteTime']))
            return json_encode(array(
                'success' => false,
                'message' => array('缺少数据, 请确保正确选择时段'),
            ));

        if (isset($post['freeTime'])) {
            $freeTime = $post['freeTime'];
            foreach ($freeTime as $each) {
                $calendar = new \Model\Calendar();
                $calendar->type = \Model\Calendar::TYPE_FREE;
                $calendar->teacher = self::$currentUser;
                $calendar->startTime = \DateTime::createFromFormat('Y-m-d H:i:s', $each['start']);
                $calendar->endTime = \DateTime::createFromFormat('Y-m-d H:i:s', $each['end']);
                $calendar->event = null;
                $calendar->save();
                \Model\Calendar::flush();
            }
        }

        if (isset($post['deleteTime'])) {
            foreach ($post['deleteTime'] as $cid) {
                $calendar = \Model\Calendar::find($cid);
                $calendar->delete();
                \Model\Calendar::flush();
            }
        }
        return json_encode(array('success' => true));
    }
}