<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\Widget;
use BaseCms\Models\Image;


class Slide extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'link',
        'image_id',
    ];

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'widget_slide');
    }
    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
