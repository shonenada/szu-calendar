<?php

namespace RBAC;

interface Role{
    static public function getInstance();
    public function getRoleName();
    public function authenticate(\Model\Account $user=null);
}
