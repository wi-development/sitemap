<?php

namespace App\Templates\Posts;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    //
    protected $table = 'tmp_post_vacancies';

    public $timestamps = false;

    protected $fillable = [
        'posttranslation_id',
        'mail_to',
        'vacancy_test_body',

    ];
}
