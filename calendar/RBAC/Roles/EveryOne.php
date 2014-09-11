<?php

namespace RBAC\Roles;
use RBAC\Role;

class EveryOne implements Role {

    private static $instance = null;
    protected $roleName = "EveryOne";
    protected $parentName = null;

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

    public function getParentName() {
        return $this->parentName;
    }

    public function authenticate(\Model\Account $user=null) {
        return true;
    }

}
