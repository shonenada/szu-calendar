<?php

namespace RBAC\Roles;
use RBAC\Role;

class Guest extends RoleBase {

    protected $roleName = "Guest";
    protected static $instance = null;

    public function authenticate(\Model\Account $user=null) {
        return true;
    }

}
