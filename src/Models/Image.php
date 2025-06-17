<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\Header;


class Image extends Model
{
        protected $fillable = [
            'image_path',
            'image_alt',
            'title',
            'credits',
        ];

    public function headers()
    {
        return $this->hasMany(Header::class, 'logo_image_id');
    }
}
