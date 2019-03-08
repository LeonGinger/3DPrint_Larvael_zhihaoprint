<?php

namespace App\Http\Transformers;

use App\Models\Customer;

class CustomersTransformer extends Transformer
{
    public function transform(Customer $customers)
    {
        return [
            'id' => $customers->id,
            'name' => $customers->name,
            'address' => $customers->address,
            'tel' => $customers->tel,
            'no' => $customers->no,
            'status' => $customers->status
        ];
    }
}
