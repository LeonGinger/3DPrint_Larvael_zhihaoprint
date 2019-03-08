<?php

namespace App\Http\Transformers;

use App\Models\Admin;

class AdminUserTransformer extends Transformer
{
    public function transform(Admin $admin)
    {
        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'last_login_at' => $admin->last_login_at,
            'action_log' => $admin->action_log,
        ];
    }
}
