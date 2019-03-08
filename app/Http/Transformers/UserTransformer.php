<?php

namespace App\Http\Transformers;
use App\Models\User;

class UserTransformer extends Transformer
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'tenant_id' => $user->tenant_id,
            'dep_ids' => $user->dep_ids,
            'pr_id' => $user->pr_id,
            'phone' => $user->phone,
            'last_login_at' => $user->last_login_at,
            'action_log' => $user->action_log,
            'status' => $user->status,
        ];
    }
}
