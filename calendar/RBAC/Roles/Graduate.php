<?php

namespace RBAC\Roles;
use RBAC\Role;

class Graduate extends RoleBase {

    protected $roleName = "Graduate";
    protected static $instance = null;

    public function authenticate(\Model\Account $account=null) {
        return $account != null && $account->rankNum == '02';
    }

}