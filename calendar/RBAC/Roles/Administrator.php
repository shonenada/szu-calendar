<?php

namespace RBAC\Roles;

class Administrator extends RoleBase {

    protected $roleName = "Administrator";

    public function authenticate(\Model\Account $user=null) {
        return $user != null && $user->isAdmin();
    }
}
