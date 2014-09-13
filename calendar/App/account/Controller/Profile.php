<?php

namespace Controller;

class Profile extends \Controller\BaseController {

    static public $url = '/account/profile';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate', 'Teacher', 'Administrator'),
        'POST' => array('Undergraduate', 'Graduate', 'Teacher', 'Administrator'),
    );

    static public function get() {
        return self::render('account/profile.html', get_defined_vars());
    }

    static public function post() {
        $errors = self::_validate(self::$request->post());

        if ($errors) {
            return self::render('account/profile.html', get_defined_vars());
        }

        $account = self::$currentUser;
        if (!isset($account->username) || strlen($account->username) == 0) {
            $account->username = self::$request->post('username');
        }
        $account->email = self::$request->post('email');
        $account->phone = self::$request->post('phone');
        $account->shortPhone = self::$request->post('shortPhone');
        $account->save();
        \Model\Account::flush();
        self::flash('修改成功');
        self::redirect(self::urlFor('account.profile[get]'));
    }

    static public function _validate($data) {
        $validation = \Extension\Validation::factory($data)
            ->label('email', '邮箱')
            ->rule('email', 'email')

            ->label('phone', '长号')
            ->rule('phone', 'phone')

            ->label('shortPhone', '短号')
            ->rule('shortPhone', 'digit')
            ->rule('shortPhone', 'min_length', array(':value', 3))
            ->rule('shortPhone', 'max_length', array(':value', 6));
        $validation->check();
        return $validation->errors('account.msg');
    }
}
