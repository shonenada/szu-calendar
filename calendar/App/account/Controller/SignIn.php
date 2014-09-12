<?php

namespace Controller;
use \Model\Account;

class SignIn extends \Controller\BaseController {

    static public $url = '/account/signin';
    static public $allow = array(
        'GET' => array('EveryOne',),
        'POST' => array('EveryOne',),
    );

    static public function get() {
        self::redirectIfSignIned(self::urlFor('account.profile[get]'));
        return self::render('account/signin.html', get_defined_vars());
    }

    static public function post() {
        $errors = self::_validate(self::$request->post());
        if ($errors) {
            return self::render('account/signin.html', get_defined_vars());
        }

        $username = self::$request->post('username');
        $password = self::$request->post('password');

        $account = \Model\Account::authenticate($username, $password);

        if ($account === false) {
            self::flash('用户名或密码错误', 'danger');
            return self::render('account/signin.html');
        }
        if ($account === null) {
            self::flash('用户禁用，请联系管理员', 'warning');
            return self::render('account/signin.html');
        }
        if ($account) {
            $account->login(self::$app);
            self::flash('登录成功', 'success');
            return self::redirect(self::urlFor('account.profile[get]'));
        }
        self::flash('登录失败', 'warning');
        return self::redirect(self::urlFor('account.sign_in[get]'));
    }

    static public function _validate($data) {
        $validation = \Extension\Validation::factory($data)
            ->label('username', '用户名')
            ->rule('username', 'not_empty')

            ->label('password', '密码')
            ->rule('password', 'not_empty')
            ->rule('password', 'min_length', array(':value', 6))
            ->rule('password', 'max_length', array(':value', 20));
        $validation->check();
        return $validation->errors('account.msg');
    }
}
