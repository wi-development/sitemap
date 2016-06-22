<?php

namespace WI\Sitemap;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SitemapTranslation extends Model
{

    protected $table = 'sitemaptranslations';

    protected $fillable = [
        'sitemap_id',
        'locale_id',
        'name',
        'content',
        'slug',
        'class',
        'published_at',
        'published_until',
        'title',
        'description',
        'keywords'
    ];


    //touch partent timestamps
    protected $touches = array('sitemap');


    //protected $dates = ['published_at','published_until']; // Treat all dates as instances of Carbon

    public function getDates()
    {
        return ['published_at','published_until'];
    }


    public function setPublishedAtAttribute($date)
    {
        try {
            (Carbon::createFromFormat('Y-m-d H:i:s', $date));
        } catch(\Exception $e) {
            $date = Carbon::now();
        }
        $this->attributes['published_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $date);

        // $this->attributes['published_at'] = Carbon::parse($date);  // Set date at midnight


    }

    public function setPublishedUntilAttribute($date)
    {
        //dc('stop'.$date);

        try {
            (Carbon::createFromFormat('Y-m-d H:i:s', $date));
        } catch(\Exception $e) {
            $date = Carbon::now()->addYears(20);
        }
        //if ((is_null($date)) || ($date == "")){
        //    $date = Carbon::now()->addYears(20);
        //}

        $this->attributes['published_until'] = Carbon::createFromFormat('Y-m-d H:i:s', $date);

        // $this->attributes['published_at'] = Carbon::parse($date);  // Set date at midnight


    }
    //relations

    //translation belongs to Sitemap
    public function sitemap(){
        return $this->belongsTo('WI\Sitemap\Sitemap'); //foreign key belongsTo
    }

    public function locale()
    {
        return $this->belongsTo('WI\Locale\Locale');
    }



    public function mediatranslations()
    {
        return $this->belongsToMany('WI\Media\MediaTranslation', 'sitemaptranslation_mediatranslation', 'sitemaptranslation_id','mediatranslation_id')
            ->withPivot('field_name','order_by_number')->orderBy('pivot_order_by_number','ASC');
    }

    //geen nut
    public function mediatranslationUIT()
    {
        return $this->belongsToMany('App\MediaTranslation', 'sitemaptranslation_mediatranslation', 'sitemaptranslation_id','mediatranslation_id')
            ->withPivot('field_name','order_by_number')
            ->where('locale_id',config('app.locale_id'))
            ->orderBy('pivot_order_by_number','ASC');
    }


    //templates method name = template->slug
    public function homepage()
    {
        return $this->hasOne('WI\Sitemap\Templates\Pages\Homepage','sitemaptranslation_id');
        //return $this->hasOne('App\Templates\Pages\Homepage','sitemaptranslation_id','id','tmp_stm_homepage');
        //s->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');
    }

    public function defaultpage()
    {
        return $this->hasOne('WI\Sitemap\Templates\Pages\Defaultpage','sitemaptranslation_id');
    }

    public function locationpage()
    {
        return $this->hasOne('WI\Sitemap\Templates\Pages\Locationpage','sitemaptranslation_id');
    }

    public function event()
    {
        return $this->hasOne('WI\Sitemap\Templates\Pages\Eventpage','sitemaptranslation_id');
    }

    //sja..
    public function newslist()
    {
        return $this->hasOne('WI\Sitemap\Templates\Pages\Defaultpage','sitemaptranslation_id');
    }

}
