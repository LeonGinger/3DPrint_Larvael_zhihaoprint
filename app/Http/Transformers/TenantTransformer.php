<?php

namespace App\Http\Transformers;

use App\Models\Tenant;

class TenantTransformer extends Transformer
{
    public function transform(Tenant $tenant)
    {
        return [
            'id' => $tenant->id,
            'linkman' => $tenant->linkman,
            'phone' => $tenant->phone,
            'expired_at' => $tenant->expired_at,
            'tenant_level_id' => $tenant->tenant_level_id,
            'tenant_type_id' => $tenant->tenant_type_id,
            'status' => $tenant->status,
        ];
    }
}
