<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\Widget;
use BaseCms\Models\SubCategory;


class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'category_widget');
    }
    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'parent_cat_id');
    }
}
