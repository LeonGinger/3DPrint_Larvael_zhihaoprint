<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;

    protected function getAdminUser()
    {
        return auth($this->guard)->user();
    }
}
