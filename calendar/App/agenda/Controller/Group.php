<?php

namespace Controller;

class Group extends \Controller\BaseController {

    static public $url = '/agenda/group/:gid';
    static public $conditions = array('gid' => '\d+');
    static public $allow = array(
        'GET' => array('Teacher'),
        'DELETE' => array('Teacher'),
    );

    static public function get($gid) {
        $currentGroup = \Model\AccountGroup::getByArray(
            array('id' => $gid, 'ownerId' =>self::$currentUser->id));

        if (!$currentGroup)
            return self::redirect(self::urlFor('agenda.group_index[get]'));

        $groupManageBaseURL = self::urlFor('agenda.group[get]', array('gid' => ''));
        $groups = \Model\AccountGroup::getUserGroup(self::$currentUser);
        $students = \Model\Account::getStudentList();
        return self::render('agenda/group.html', get_defined_vars());
    }

    static public function delete($gid) {
        $group = \Model\AccountGroup::getByArray(
            array('id' => $gid, 'ownerId' =>self::$currentUser->id));

        if (!$group) {
            return json_encode(array(
                'success' => false,
                'message' => array('分组不存在'),
            ));
        }

        if ($group->ownerId != self::$currentUser->id) {
            return json_encode(array(
                'success' => false,
                'message' => array('您无权限'),
            ));
        }

        $group->delete();
        \Model\AccountGroup::flush();

        return json_encode(array(
            'success' => true,
        ));
    }

}