<?php

namespace RBAC\Roles;
use RBAC\Role;

class NormalUser extends EveryOne {

    protected $roleName = "NormalUser";
    protected $parentName = "EveryOne";

    public function getRoleName() {
        return $this->roleName;
    }

    public function getParentName() {
        return $this->parentName;
    }

    public function authenticate(\Model\Account $user=null) {
        return $user != null;
    }

}