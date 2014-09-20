<?php

namespace Controller;

class WorkArrangement extends \Controller\BaseController {

    static public $url = '/agenda/work-arrangment';
    static public $allow = array(
        'GET' => array('Teacher'),
        'POST' => array('Teacher'),
    );

    static public function get() {
        $groups = \Model\AccountGroup::getUserGroup(self::$currentUser);
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
                $visibleGroups = new \Doctrine\Common\Collections\ArrayCollection();
                if (isset($each['visiableGroup'])) {
                    foreach ($each['visiableGroup'] as $gid) {
                        $group = \Model\AccountGroup::find($gid);
                        $visibleGroups[] = $group;
                    }
                }

                $calendar = new \Model\Calendar();
                $calendar->type = \Model\Calendar::TYPE_FREE;
                $calendar->account = self::$currentUser;
                $calendar->title = $each['title'];
                $calendar->visibleGroups = $visibleGroups;
                $calendar->description = $each['description'];
                $calendar->workPlace = $each['place'];
                $calendar->startTime = \DateTime::createFromFormat('Y-m-d H:i:s', $each['start']);
                $calendar->endTime = \DateTime::createFromFormat('Y-m-d H:i:s', $each['end']);
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