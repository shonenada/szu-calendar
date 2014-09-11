<?php

/**
 * \Slim\Slim 扩展类
 * @author shonenada
 * 
 */


class Slimx extends \Slim\Slim {

    protected $webRoot = '/';
    protected $installedApps = array();
    protected $permissions = array();
    protected $auth = null;

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

    public function setRoot($root) {
        $this->webRoot = $root;
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

        if (!$vars) {
            return ;
        }

        if (!array_key_exists('url', $vars)) {
            return ;
        }

        $resource = $url = preg_replace('/\/+/', '/', $this->webRoot . $vars['url']);
        if (array_key_exists('name', $vars)) {
            $name = $vars['name'];
        } else {
            $name = $this->generate_name($controller);
        }

        if (method_exists($controller, 'get')) 
            $handler = $this->get($url, "$controller::_get")->name("${moduleName}.{$name}[get]");

        if (method_exists($controller, 'post'))
            $handler = $this->post($url, "$controller::_post")->name("${moduleName}.{$name}[post]");

        if (method_exists($controller, 'put'))
            $handler = $this->put($url, "$controller::_put")->name("${moduleName}.{$name}[put]");

        if (method_exists($controller, 'delete'))
            $handler = $this->delete($url, "$controller::_delete")->name("${moduleName}.{$name}[delete]");

        if (array_key_exists('conditions', $vars)) {
            $handler->conditions($vars['conditions']);
            foreach ($vars['conditions'] as $key => $value) {
                $resource = str_replace($key, $value, $resource);
            }
        }

        $resource = preg_replace('/:[^\/]+/', '\S+', $resource);

        if (array_key_exists('allow', $vars)) {
            foreach ($vars['allow'] as $method => $roles) {
                foreach ($roles as $roleName){
                    $path = sprintf("\\RBAC\\Roles\\%s::getInstance", $roleName);
                    $role = call_user_func($path);
                    $this->auth->allow($role, $resource, $method);
                }
            }
        }

        if (array_key_exists('deny', $vars)) {
            foreach ($vars['deny'] as $method => $roles) {
                foreach ($roles as $roleName){
                    $path = sprintf("\\RBAC\\Roles\\%s::getInstance", $roleName);
                    $role = call_user_func($path);
                    $this->auth->deny($role, $resource, $method);
                }
            }
        }

    }

}
