<?php

namespace RBAC\Roles;
use RBAC\Role;

class Guest extends RoleBase {

    private static $instance = null;
    protected $roleName = "Guest";

    public function authenticate(\Model\Account $user=null) {
        return true;
    }

}
