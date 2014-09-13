<?php

namespace RBAC\Roles;
use RBAC\Role;

class RoleBase implements Role {

    private static $instance = null;
    protected $roleName = null;

    public static function getInstance() {
        if (!isset(self::$instance) || self::$instance == null) {
            $class = get_called_class();
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
