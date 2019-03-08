<?php

namespace App\Http\Transformers;

use App\Models\Materials;

class MaterialsTransformer extends Transformer
{
    public function transform(Materials $materials)
    {
        return [
            'id' => $materials->id,
            'name' => $materials->name,
            'price' => $materials->price,
            'density' => $materials->density,
            'shape' => $materials->shape,
            'mold_id' => $materials->mold_id,
            'status' => $materials->status
        ];
    }
}
