<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_variant_item_id', 'quantity', 'price',
    ];
}
