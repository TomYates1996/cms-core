<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\VariantType;
use BaseCms\Models\ProductVariant;


class VariantOption extends Model
{
    public function type()
    {
        return $this->belongsTo(VariantType::class);
    }

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_option');
    }
}
