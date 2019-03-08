<?php

namespace App\Http\Middleware;


class UserRoleAction extends Middleware
{
    protected $get = array(["check","add","delete","edit"]);
}
