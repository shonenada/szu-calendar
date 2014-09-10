<?php

/**
 * \Slim\Slim 扩展类
 * @author shonenada
 * 
 */


class Slimx extends \Slim\Slim {

    protected $installedApps = array();

    private function __autoloader($class) {
        global $installedApps;
        $cls = str_replace('\\', '/', $class);
        foreach ($installedApps as $app) {
            $path = sprintf(APPROOT . "App/%s/%s.php", strtolower($app), $cls);
            if (is_file($path)) {
                require $path;
            }
        }
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
        return $name;
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

    protected function scanController($moduleName){
        $controllerPath = APPROOT . DIRECTORY_SEPARATOR . "App/${moduleName}/Controller/*";
        $this->scanDir($controllerPath, function($input) use($moduleName) {
            $input = str_replace(APPROOT . DIRECTORY_SEPARATOR . "App/${moduleName}", '', $input);
            $input = str_replace('.php', '', $input);
            $controller = str_replace('/', '\\', $input);
            $this->registerController($controller);
        });
    }

    public function installApps($installedApps) {
        spl_autoload_register(array('Slimx', '__autoloader'));
        $this->installedApps = $installedApps;
        foreach ($installedApps as $name) {
            $this->scanController($name);
        }
    }

    // 注册控制器方法，将 php 控制注册到 app 内部，并安装控制器。
    public function registerController ($controller) {
        $vars = get_class_vars($controller);

        if (!$vars) {
            return ;
        }

        if (!array_key_exists('url', $vars)) {
            return ;
        }

        $url = $vars['url'];
        $name = $this->generate_name($controller);

        if (method_exists($controller, 'get'))
            $handler = $this->get($url, "$controller::_get")->name("{$name}_get");

        if (method_exists($controller, 'post'))
            $handler = $this->post($url, "$controller::_post")->name("{$name}_post");

        if (method_exists($controller, 'put'))
            $handler = $this->put($url, "$controller::_put")->name("{$name}_put");

        if (method_exists($controller, 'delete'))
            $handler = $this->delete($url, "$controller::_delete")->name("{$name}_delete");

        if (array_key_exists('conditions', $vars))
            $handler->conditions($vars['conditions']);
    }

}