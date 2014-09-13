<?php

namespace RBAC;

class Authentication{

    public $ptable = array();
    private $redirect = array();

    public function allow(Role $role, $resource, $method){
        $this->record($role, $resource, $method, 'allow');
        return $this;
    }

    public function deny(Role $role, $resource, $method){
        $this->record($role, $resource, $method, 'deny');
        return $this;
    }

    public function accessiable(\Model\Account $user=null, $resource=null, $method=null){

        $auth = function(Role $r=null, \Model\Account $u=null){
            return $r ? $r->authenticate($u) : false;
        };

        $allow = array_filter($this->ptable, function($i) use ($user, $resource, $method, $auth){
            $pattern = sprintf("/^%s$/", str_replace('/', '\/', $i['resource']));
            preg_match($pattern, $resource, $matches);
            return $matches != null
                && ('*' == $i['method'] || strtolower($i['method']) == strtolower($method))
                && $auth($i['role'], $user)
                && $i['action'] == 'allow';
        });
        
        $deny = array_filter($this->ptable, function($i) use ($user, $resource, $method, $auth){
            $pattern = sprintf("/^%s$/", str_replace('/', '\/', $i['resource']));
            preg_match($pattern, $resource, $matches);
            return $matches != null
                && ('*' == $i['method'] || strtolower($i['method']) == strtolower($method))
                && $auth($i['role'], $user)
                && $i['action'] == 'deny';
        });

        return (!empty($allow) && empty($deny));

    }

    private function record(Role $role, $resource, $method, $action){
        $key = "{$action}-{$role->getRoleName()}-{$method}-{$resource}";
        $this->ptable[$key] = array(
            "role" => $role,
            "resource" => $resource,
            "method" => $method,
            "action" => $action,
        );
    }

}
