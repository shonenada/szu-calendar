<?php

namespace Controller;

class Password extends \Controller\BaseController {

    static public $url = '/account/password';
    static public $allow = array(
        'GET' => array('Undergraduate', 'Graduate', 'Teacher', 'Administrator'),
        'POST' => array('Undergraduate', 'Graduate', 'Teacher', 'Administrator'),
    );

    static public function get() {
        return self::render('account/password.html', get_defined_vars());
    }

    static public function post() {
        $errors = self::_validate(self::$request->post());

        if ($errors) {
            return self::render('account/password.html', get_defined_vars());
        }

        $account = self::$currentUser;
        $password = self::$request->post('old_password');

        if (!$account->checkPassword($password)) {
            self::flash('原始密码错误!');
            return self::render('account/password.html', get_defined_vars());
        }

        $account->setPassword(self::$request->post('confirm_password'));
        $account->save();
        \Model\Account::flush();
        self::flash('修改成功');
        self::redirect(self::urlFor('account.password[get]'));
    }

    static public function _validate($data) {
        $validation = \Extension\Validation::factory($data)
            ->label('old_password', '密码')
            ->rule('old_password', 'min_length', array(':value', 6))
            ->rule('old_password', 'max_length', array(':value', 20))

            ->label('new_password', '密码')
            ->rule('new_password', 'not_empty', array(':value', 6))
            ->rule('new_password', 'min_length', array(':value', 6))
            ->rule('new_password', 'max_length', array(':value', 20))

            ->label('confirm_password', '确认密码')
            ->rule('confirm_password', 'min_length', array(':value', 6))
            ->rule('confirm_password', 'max_length', array(':value', 20))
            ->rule('confirm_password', 'matches', array(':validation', 'confirm_password', 'new_password'));
        $validation->check();
        return $validation->errors('account.msg');
    }
}
