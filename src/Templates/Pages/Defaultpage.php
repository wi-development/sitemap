<?php

namespace WI\Sitemap\Templates\Pages;

use Illuminate\Database\Eloquent\Model;

class Defaultpage extends Model
{
    protected $table = 'tmp_page_default';

    public $timestamps = false;

    protected $fillable = [
        'sitemaptranslation_id',
        'subtitle',
        'content',
    ];
}