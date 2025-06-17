<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\Category;


class SubCategory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_cat_id'];

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_cat_id');
    }
}
