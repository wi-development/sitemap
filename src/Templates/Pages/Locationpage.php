<?php

namespace WI\Sitemap\Templates\Pages;

use Illuminate\Database\Eloquent\Model;

class Locationpage extends Model
{
    protected $table = 'tmp_page_location';

    public $timestamps = false;

    protected $fillable = [
        'sitemaptranslation_id',
        'subtitle',
        'content',
        'content_1',
        'name',
        'address',
        'postal_code',
        'state_region',
        'city',
        'country',
        'phone',
        'email'
    ];
}