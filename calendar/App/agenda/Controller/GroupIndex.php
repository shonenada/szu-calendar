<?php

namespace Controller;

class GroupIndex extends \Controller\BaseController {

    static public $url = '/agenda/group/';
    static public $allow = array(
        'GET' => array('Teacher'),
        'POST' => array('Teacher'),
    );

    static public function get() {
        $groups = \Model\AccountGroup::getUserGroup(self::$currentUser);
        $groupManageBaseURL = self::urlFor('agenda.group[get]', array('gid' => ''));
        return self::render('agenda/group.html', get_defined_vars());
    }

    static public function post() {
        $errors = self::_validate(self::$request->post());
        if ($errors) {
            return json_encode(array(
                'success' => false,
                'message' => array_values($errors),
            ));
        }
        
        $name = self::$request->post('name');
        $isExist = \Model\AccountGroup::isExistByArray(array(
            'name' => $name,
            'ownerId' => self::$currentUser->id));
        if ($isExist) {
            return json_encode(array(
                'success' => false,
                'message' => array('分组已存在'),
            ));
        }

        $remark = self::$request->post('remark');
        $group = new \Model\AccountGroup();
        $group->owner = self::$currentUser;
        $group->name = $name;
        $group->type = \Model\AccountGroup::TYPE_STUDENT_GROUP;
        $group->remark = $remark;
        $group->save();
        \Model\AccountGroup::flush();
        return json_encode(array(
            'success' => true,
        ));
    }

    static public function _validate($data) {
        $validation = \Extension\Validation::factory($data)
            ->label('name', '分组名')
            ->rule('name', 'not_empty')
            ->rule('name', 'min_length', array(':value', 1))
            ->rule('name', 'max_length', array(':value', 30));
        $validation->check();
        return $validation->errors('chinese');
    }
}