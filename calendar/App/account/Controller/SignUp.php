<?php

namespace Controller;

class SignUp extends \Controller\BaseController {

    static public $url = '/account/signup';
    static public $allow = array(
        'GET' => array('Guest',),
        'POST' => array('Guest',),
    );

    static public function get() {

        if (!isset($_SESSION['cas'])) {
            return self::redirect(self::urlFor('account.cas[get]'));
        }

        $name = $_SESSION['cas']['Pname'];
        $gender = $_SESSION['cas']['SexName'];
        $szuno = $_SESSION['cas']['StudentNo'];
        $rankNum = $_SESSION['cas']['RankName'];

        $account = new \Model\Account();

        return self::render('account/signup.html', get_defined_vars());
    }

    static public function post() {

        $casData = array(
            'name' => $_SESSION['cas']['Pname'],
            'college' => $_SESSION['cas']['OrgName'],
            'gender' => $_SESSION['cas']['SexName'],
            'szuno' => $_SESSION['cas']['StudentNo'],
            'cardId' => $_SESSION['cas']['ICAccount'],
            'identityNumber' => $_SESSION['cas']['PersonalId'],
            'rankNum' => $_SESSION['cas']['RankName'],
        );

        $errors = self::_validate(self::$request->post());

        if ($errors) {
            return self::render('account/signup.html', get_defined_vars());
        }

        if (\Model\Account::isExist(self::$request->post('username'))){
            self::flash('用户名已存在', 'danger');
            return self::render('account/signup.html', get_defined_vars());
        }

        $accountData = array_merge(self::$request->post(), $casData);
        $account = \Model\Account::factory($accountData);
        $account->save();
        \Model\Account::flush();
        self::flash('注册成功');
        self::redirect(self::urlFor('account.sign_in[get]'));
    }

    static public function _validate($data) {
        $validation = \Extension\Validation::factory($data)
            ->label('username', '用户名')
            ->rule('username', 'not_empty')

            ->label('password', '密码')
            ->rule('password', 'not_empty')
            ->rule('password', 'min_length', array(':value', 6))
            ->rule('password', 'max_length', array(':value', 20))

            ->label('email', '密码')
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
