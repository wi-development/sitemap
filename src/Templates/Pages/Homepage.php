<?php

namespace WI\Sitemap\Templates\Pages;

use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    //
    protected $table = 'tmp_page_homepage';

    public $timestamps = false;

    protected $fillable = [
        'sitemaptranslation_id',
        'subtitle',
        'content',

    ];
}


