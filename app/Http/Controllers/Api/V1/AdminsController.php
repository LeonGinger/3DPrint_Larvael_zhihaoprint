<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformers\AdminUserTransformer;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    public function me()
    {
        $admin_user = $this->getAdminUser();
        return $this->response->item($admin_user, new AdminUserTransformer());
    }
}
