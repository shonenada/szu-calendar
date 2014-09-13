<?php

namespace RBAC\Roles;

class Administrator extends RoleBase {

    protected $roleName = "Administrator";
    protected static $instance = null;

    public function authenticate(\Model\Account $user=null) {
        return $user != null && $user->isAdmin();
    }
}
