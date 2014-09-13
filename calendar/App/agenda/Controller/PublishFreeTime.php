<?php

namespace Controller;

class PublishFreeTime extends \Controller\BaseController {

    static public $url = '/agenda/publish-free-time';
    static public $allow = array(
        'GET' => array('Teacher'),
        'POST' => array('Teacher'),
    );

    static public function get() {
        return self::render('agenda/publish_free_time.html', get_defined_vars());
    }

    static public function post() {
        $post = self::$request->post();
        var_dump($post);
    }
}
