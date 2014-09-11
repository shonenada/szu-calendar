<?php

namespace Controller;

class SignUp extends \Controller\BaseController {

    static public $url = '/account/signup';

    static public function get() {

        if (!isset($_SESSION['cas'])) {
            return self::redirect(self::urlFor('account.sign_in[get]'));
        }

        $name = $_SESSION['cas']['Pname'];
        $college = $_SESSION['cas']['OrgName'];
        $gender = $_SESSION['cas']['SexName'];
        $szuno = $_SESSION['cas']['StudentNo'];
        $card_id = $_SESSION['cas']['ICAccount'];
        $identityNumber = $_SESSION['cas']['PersonalId'];
        $rankNum = $_SESSION['cas']['RankName'];

        $account = new \Model\Account();

        return self::render('account/signup.html', get_defined_vars());
    }

    static public function post() {
        $error = self::_validate(self::$request->post());
        var_dump($error);
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
                    ->rule('shortPhone', 'max_length', array(':value', 6))
                    ;
        $validation->check();
        return $validation->errors();
    }

}
