<?php

namespace Extension;

class Auth {

    static public function setup($app) {
        $app->hook("slim.before.dispatch", function () use ($app){
            $uid = $app->getCookie("userid");
            $ip = $app->request->getIp();
            $token = $app->getCookie("token");
            if (empty($uid)){
                $user = null;
            }else {
                $user = \Model\Account::find($uid);
                if ($user && !($user->validateToken($token) && $user->validateIp($ip))) {
                    $user = null;
                }
            }
            $resource = $app->request->getPath();
            $method = $app->request->getMethod();
            if (!$app->accessiable($user, $resource, $method)) {
                $app->redirect($app->urlFor('master.error-403[get]'));
            }
        });
    }

}