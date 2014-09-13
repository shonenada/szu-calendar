<?php

namespace RBAC\Roles;
use RBAC\Role;

class Undergraduate extends RoleBase {

    protected $roleName = "Undergraduate";
    protected static $instance = null;

    public function authenticate(\Model\Account $account=null) {
        return $account != null && $account->rankNum == '01';
    }

}