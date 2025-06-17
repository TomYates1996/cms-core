<?php

namespace BaseCms\Models;


use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\VariantOption;

class VariantType extends Model
{
    public function options()
    {
        return $this->hasMany(VariantOption::class);
    }
}
