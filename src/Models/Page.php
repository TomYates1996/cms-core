<?php

namespace BaseCms\Models;

use Illuminate\Database\Eloquent\Model;
use BaseCms\Models\Widget;
use BaseCms\Models\Header;
use BaseCms\Models\Footer;

class Page extends Model
{
    protected $fillable = [
        'title',  
        'slug',  
        'created_by',
        'show_in_nav', 
        'section',
        'level',
        'parent_id',
        'is_link',
        'linked_page_id',
    ];
    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'page_widget')->withPivot('position', 'visibility')->orderBy('page_widget.position'); 
    }
    public function headers()
    {
        return $this->belongsToMany(Header::class);
    }
    public function footers()
    {
        return $this->belongsToMany(Footer::class, 'footer_page');
    }
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }
}
