<?php

namespace RBAC\Roles;
use RBAC\Role;

class Teacher extends RoleBase {

    protected $roleName = "Teacher";
    protected static $instance = null;

    public function authenticate(\Model\Account $account=null) {
        return $account != null && $account->rankNum == '05';
    }

}