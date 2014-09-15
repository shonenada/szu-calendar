<?php

/**
 * \Slim\Slim 扩展类
 * @author shonenada
 * 
 */


class Slimx extends \Slim\Slim {

    protected $installedApps = array();
    protected $permissions = array();
    public $auth = null;

    public function __construct(array $userSettings = array()) {
        parent::__construct($userSettings);
        $this->auth = new \RBAC\Authentication();
    }

    public function __autoloader($class) {
        global $installedApps;
        $cls = str_replace('\\', '/', $class);
        foreach ($installedApps as $app) {
            $path = sprintf(APPROOT . "App/%s/%s.php", strtolower($app), $cls);
            if (is_file($path)) {
                require $path;
            }
        }
    }

    public function accessiable($user, $resource, $method) {
        return $this->auth->accessiable($user, $resource, $method);
    }

    private function scanDir($path, $callback) {
        foreach (glob($path) as $filename){
            if (is_dir($filename)){
                $this->scanDir($filename . "/*", $callback);
            } else {
                if (substr($filename, -3) == 'php'){
                    $callback($filename);
                }
            }
        }
    }

    private function generate_name($controller) {
        $name = $this->camel2underline($controller);
        $name = explode('\\', $name);
        $name = array_pop($name);
        return strtolower($name);
    }

    private function camel2underline($camel) {
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $camel));
    }

    // 重写 mapRoute，适应 Routex 类。
    protected function mapRoute($args)
    {
        $pattern = array_shift ($args);
        $callable = array_pop ($args);
        $route = new Routex ($pattern, $callable, $this->settings['routes.case_sensitive']);
        $this->router->map ($route);
        if (count($args) > 0) {
            $route->setMiddleware ($args);
        }

        return $route;
    }

    protected function loadModule($moduleName){
        $controllerPath = APPROOT . "App/${moduleName}/Controller/*";
        $_self = $this;
        $this->scanDir($controllerPath, function ($input) use($moduleName, $_self) {
            $input = str_replace(APPROOT . "App/${moduleName}", '', $input);
            $input = str_replace('.php', '', $input);
            $controller = str_replace('/', '\\', $input);
            $_self->registerController($controller, $moduleName);
        });
    }

    private function __flashArr($key, $value, $when) {
        if (isset($this->environment['slim.flash'])) {
            $messages = $this->environment['slim.flash']->getMessages();

            if (isset($messages[$key]))
                $msg = $messages[$key];
            else
                $msg = array();

            array_push($msg, $value);
            if ($when == 'now') {
                $this->environment['slim.flash']->now($key, $msg);
            } else if($when == 'next' ) {
                $this->environment['slim.flash']->set($key, $msg);
            }
        }
    }

    public function flashArr($key, $value) {
        $this->__flashArr($key, $value, 'now');
    }

    public function flashNextArr($key, $value) {
        $this->__flashArr($key, $value, 'next');
    }

    public function flashRedirectKeep() {
        if (isset($this->environment['slim.flash'])) {
            $messages = $this->environment['slim.flash']->getMessages();
            foreach ($messages as $key => $msg)
                $this->environment['slim.flash']->set($key, $msg);
        }
    }

    public function installApps($installedApps) {
        spl_autoload_register(array('Slimx', '__autoloader'));
        $this->installedApps = $installedApps;
        foreach ($installedApps as $name) {
            $this->loadModule($name);
        }
    }

    // 注册控制器方法，将 php 控制注册到 app 内部，并安装控制器。
    public function registerController ($controller, $moduleName) {
        $vars = get_class_vars($controller);
        if (!$vars)
            return ;

        if (!array_key_exists('url', $vars)) 
            return ;

        $this->_saveRoute($controller, $moduleName, $vars);
        $this->_savePermission($vars);
    }

    private function _saveRoute($controller, $moduleName, $vars) {
        $url = preg_replace('/\/+/', '/', $vars['url']);
        if (array_key_exists('name', $vars)) {
            $name = $vars['name'];
        } else {
            $name = $this->generate_name($controller);
        }

        $handlers = array();

        if (method_exists($controller, 'get')) 
            $handlers['get'] = $this->get($url, "$controller::_get")->name("${moduleName}.{$name}[get]");

        if (method_exists($controller, 'post'))
            $handlers['post'] = $this->post($url, "$controller::_post")->name("${moduleName}.{$name}[post]");

        if (method_exists($controller, 'put'))
            $handlers['put'] = $this->put($url, "$controller::_put")->name("${moduleName}.{$name}[put]");

        if (method_exists($controller, 'delete'))
            $handlers['delete'] = $this->delete($url, "$controller::_delete")->name("${moduleName}.{$name}[delete]");

        if (array_key_exists('conditions', $vars)) {
            foreach ($handlers as $method => $handler) {
                $handler->conditions($vars['conditions']);
            }
        }
    }

    private function _savePermission($vars) {
        $resource = preg_replace('/\/+/', '/', $vars['url']);
        $resource = $this->request->getRootUri() . preg_replace('/:[^\/]+/', '\S+', $resource);
        
        if (array_key_exists('conditions', $vars)) {
            foreach ($vars['conditions'] as $key => $value) {
                $resource = str_replace($key, $value, $resource);
            }
        }

        if (array_key_exists('allow', $vars)) {
            foreach ($vars['allow'] as $method => $roles) {
                foreach ($roles as $roleName){
                    $role = \RBAC\Helper::getRoleByName($roleName);
                    $this->auth->allow($role, $resource, $method);
                }
            }
        }

        if (array_key_exists('deny', $vars)) {
            foreach ($vars['deny'] as $method => $roles) {
                foreach ($roles as $roleName){
                    $role = \RBAC\Helper::getRoleByName($roleName);
                    $this->auth->deny($role, $resource, $method);
                }
            }
        }
    }

}
