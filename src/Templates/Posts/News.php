<?php

namespace App\Templates\Posts;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'tmp_post_news';

    public $timestamps = false;

    protected $fillable = [
        'posttranslation_id',
        'author',
        'news_test_body',

    ];
}
