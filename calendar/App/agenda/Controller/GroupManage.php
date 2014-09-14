<?php

namespace Controller;

class GroupManage extends \Controller\BaseController {

    static public $url = '/agenda/group-manage';
    static public $allow = array(
        'GET' => array('Teacher'),
    );

    static public function get() {
        $groups = \Model\AccountGroup::getUserGroup(self::$currentUser);
        $students = \Model\Account::getStudentList();
        return self::render('agenda/group_manage.html', get_defined_vars());
    }
}