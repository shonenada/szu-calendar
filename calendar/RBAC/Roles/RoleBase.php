<?php

namespace RBAC\Roles;
use RBAC\Role;

class RoleBase implements Role {

    protected static $instance = null;
    protected $roleName = null;

    public static function getInstance() {
        $class = get_called_class();
        if (!isset($class::$instance) || $class::$instance == null) {
            self::$instance = new $class();
        }
        return self::$instance;
    }

    public function getRoleName() {
        return $this->roleName;
    }

    public function authenticate(\Model\Account $user=null) {
        return false;
    }

}
