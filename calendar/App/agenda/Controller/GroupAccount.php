<?php

namespace Controller;

class GroupAccount extends \Controller\BaseController {

    static public $url = '/agenda/group/:gid/account';
    static public $conditions = array('gid' => '\d+');
    static public $allow = array(
        'POST' => array('Teacher'),
    );

    static public function post($gid) {
        $currentGroup = \Model\AccountGroup::getByArray(
            array('id' => $gid, 'ownerId' =>self::$currentUser->id));
        if (!$currentGroup)
            return self::redirect(self::urlFor('agenda.group_index[get]'));

        $data = \Helper\Request::parseBody(self::$request->getBody());
        if (!isset($data['students'])) {
            return json_encode(array(
                'success' => false,
                'message' => array('至少保留一名学生'),
            ));
        }

        if (is_array($data['students']))
            $studentIds = $data['students'];
        else
            $studentIds = array($data['students']);

        $students = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($studentIds as $sid) {
            $student = \Model\Account::find($sid);
            $students[] = $student;
        }
        $currentGroup->accounts = $students;
        $currentGroup->save();
        \Model\AccountGroup::flush();
        return json_encode(array('success' => true));
    }

}