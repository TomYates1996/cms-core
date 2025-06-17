<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\Listing;


class Event extends Model
{
        protected $fillable = [
        'title', 
        'slug', 
        'description',
        'short_description',
        'category_id',
        'sub_category_id',
        'tags',
        'address',
        'city',
        'region',
        'country',
        'postcode',
        'latitude',
        'longitude',
        'phone_number',
        'email',
        'website',
        'media_gallery',
        'thumbnail_image',
        'prices' ,
        'booking_url',
        'reservation_email',
        'featured',
        'owner_id',
        'published_at',
        'social_links',
        'amenities',
        'accessibility_info',
        'openings',
        'all_day',
        'start_datetime',
        'end_datetime',
        'recurrence',
        'venue_name',
        'organiser_name',
        'organiser_email',
        'organiser_phone',
        'organiser_id',

    ];
    protected $casts = [
        'amenities' => 'array',
        'social_links' => 'array',
        'openings' => 'array',
        'media_gallery' => 'array',
        'prices' => 'array',
        'tags' => 'array',
        'accessibility_info' => 'array',
    ];
    
    public function listing()
    {
        return $this->belongsTo(Listing::class, 'organiser_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
}
