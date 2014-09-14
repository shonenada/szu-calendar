<?php

namespace RBAC;

class Helper {

    public static function getRoleByName($roleName) {
        $path = sprintf("\\RBAC\\Roles\\%s::getInstance", $roleName);
        $role = call_user_func($path);
        if ($role) {
            return $role;
        }
        return null;
    }
}