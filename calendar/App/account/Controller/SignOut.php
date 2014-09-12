<?php

namespace Controller;

class SignOut extends \Controller\BaseController {

    static public $url = '/account/signout';

    static public function get() {
        self::$app->deleteCookie('userid');
        self::$app->deleteCookie('token');
        self::flash('退出成功', 'info');
        self::redirect(self::urlFor('account.sign_in[get]'));
    }
}
