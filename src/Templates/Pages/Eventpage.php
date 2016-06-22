<?php

namespace WI\Sitemap\Templates\Pages;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Eventpage extends Model
{
    protected $table = 'tmp_page_event';

    public $timestamps = false;

    public function getDates()
    {
        return ['when'];
    }

    protected $fillable = [
        'sitemaptranslation_id',
        'subtitle',
        'content',
        'name',
        'genre',
        'price',
        'when',
        'website',
        'ticket_url'
    ];


    public function setPriceAttribute($price){
        //$price = '17,55';
        $price = floatval(str_replace(',','.', $price));
        $price =  number_format($price*100,0, '.', '');
        $this->attributes['price'] = $price;

    }

    public function getPriceAttribute($price){
        $price = number_format($price/100,2, ',', '');
        return $price;
    }

    public function setWhenAttribute($date)
    {
        try {
            (Carbon::createFromFormat('Y-m-d H:i:s', $date));
        } catch(\Exception $e) {
            $date = Carbon::now();
        }
        $this->attributes['when'] = Carbon::createFromFormat('Y-m-d H:i:s', $date);

        // $this->attributes['published_at'] = Carbon::parse($date);  // Set date at midnight


    }
}