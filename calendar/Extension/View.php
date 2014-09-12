<?php

namespace Extension;


class View {

    static public function setup($app) {
        $view = $app->view();
        $view->setTemplatesDirectory($app->config('templates.path'));

        $view_options = require_once(APPROOT. 'config/view.php');
        $view->parserOptions = $view_options;

        $view->parserExtensions = array(
            new \Slim\Views\TwigExtension(),
            new ViewFunctionExtension(),
            new ViewFilterExtension(),
            new ViewGlobalExtension(),
        );

        $env = $app->view()->getEnvironment();
    }

    static public function addGlobalVariable ($app, $key, $value) {
        $view = $app->view();
        $twigEnv = $view->getEnvironment();
        $twigEnv->addGlobal($key, $value);
    }

}


class ViewGlobalExtension extends \Twig_Extension {
    public function getName() {
        return 'calendar.globals';
    }

    public function getGlobals() {
        return array(
            'siteTitle' => 'SZU Calendar',
            'siteKeyword' => 'SZU Calendar',
            'siteDescription' => 'SZU Calendar',
        );
    }
}


class ViewFilterExtension extends \Twig_Extension {
    public function getName() {
        return 'calendar.filters';
    }

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('lang', array($this, 'lang')),
            new \Twig_SimpleFilter('ellipsis', array($this, 'ellipsis')),
        );
    }

    public function lang ($obj, $field, $code) {
        $lang = \Model\Lang::getByCode($code);
        foreach ($obj->translations as $tran) {
            if ($tran->lang == $lang) {
                return $tran->$field;
            }
        }
        return '';
    }

    public function ellipsis ($string, $maxLength, $ellipsisStr='...') {
        if (mb_strlen($string, 'utf-8') > $maxLength) {
            return mb_substr($string, 0, $maxLength, 'utf-8') . $ellipsisStr;
        }
        return $string;
    }
}


class ViewFunctionExtension extends \Twig_Extension {
    public function getName() {
        return 'calendar.functions';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('rootUrl', array($this, 'rootUrl')),
            new \Twig_SimpleFunction('execTime', array($this, 'execTime')),
            new \Twig_SimpleFunction('trans', array($this, 'trans')),
            new \Twig_SimpleFunction('renderCss', array($this, 'renderCss')),
            new \Twig_SimpleFunction('renderJs', array($this, 'renderJs')),
        );
    }

    public function rootUrl ($url, $withUrl = false, $withUri = true, $appName = 'default') {
        $uri = '/' . ltrim($url, '/');
        if ($withUrl) {
            $req = \Slim\Slim::getInstance($appName)->request();
            if ($withUri) {
                $uri .= $req->getRootUri();
            }
            $uri = $req->getUrl() . $uri;
        }
        return $uri;
    }

    public function trans($trans_id) {
        $app = \GlobalEnv::get('app');
        $lang = $app->getCookie('lang.code');
        if ($lang == null) {
            $lang = 'zh';
        }
        $message = require(APPROOT . 'translations/' . $lang . '/message.php');
        if (!array_key_exists($trans_id, $message)) {
            return $trans_id;
        }
        return $message[$trans_id];
    }

    public function execTime ($precision, $untilTimestamp=null) {
        return \Util\Helper::execTime($precision, $untilTimestamp);
    }

    public function renderCss ($path, $media = 'screen') {
        $css_path = $this->rootUrl('static/' . $path);
        return "<link rel=\"stylesheet\" type=\"text/css\" media=\"${media}\" href=\"${css_path}\" />";
    }

    public function renderJs ($path) {
        $script_path = $this->rootUrl('static/' . $path);
        return "<script src=\"${script_path}\"></script>";
    }

}
