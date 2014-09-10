<?php

namespace RBAC;

interface Role{
    static public function getInstance();
    public function getRoleName();
    public function getParentName();
    public function authenticate(\Model\User $user=null);
}
