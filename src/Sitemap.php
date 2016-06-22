<?php

namespace WI\Sitemap;

use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    protected $fillable = [
        //'system_name',
        'parent_id',
        'order_by_number',
        'depth',
        'online',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
        'template_id',
        'form'
    ];


    //relations

    public function translations()
    {
        return $this->hasMany('WI\Sitemap\SitemapTranslation');
    }

    public function translation()
    {
        return $this->hasOne('WI\Sitemap\SitemapTranslation')->where('locale_id',config('app.locale_id'));
    }

    public function testTranslation(){
        return $this->belongsTo('App\SitemapTranslation','sitemap_id')->where('locale_id',config('app.locale_id'));;
    }




    public function orders()
    {
        return 'asdf';
    }
    public function user(){
        return $this->belongsTo('WI\User\User','updated_by_user_id');
    }

    public function template(){
        return $this->belongsTo('WI\Core\Entities\Template\Template');
    }

    public function references(){
        return $this->belongsToMany('WI\Core\Entities\Reference\Reference','sitemap_reference')->withPivot('component_id','order_by_number')->orderBy('order_by_number','ASC');
    }

    public function categories(){
        return $this->hasMany('WI\Sitemap\Category','sitemap_id');
            //->where('category','locatie');

        return $this->belongsToMany('App\Category', 'sitemap_category', 'sitemap_id')
            ->withPivot('category','order_by_number')->orderBy('pivot_order_by_number','ASC');

        //return $this->belongsToMany('App\MediaTranslation', 'sitemaptranslation_mediatranslation', 'sitemaptranslation_id','mediatranslation_id')
        //    ->withPivot('field_name','order_by_number')->orderBy('pivot_order_by_number','ASC');
    }

    public function categoriesTest(){
        return $this->belongsToMany('App\Sitemap','sitemap_category','sitemap_category_id','sitemap_id');
        //->where('status','=','online');
        //->where('category','=','locatie');
        //->where('category','locatie');

        return $this->belongsToMany('App\Category', 'sitemap_category', 'sitemap_id')
            ->withPivot('category','order_by_number')->orderBy('pivot_order_by_number','ASC');

        //return $this->belongsToMany('App\MediaTranslation', 'sitemaptranslation_mediatranslation', 'sitemaptranslation_id','mediatranslation_id')
        //    ->withPivot('field_name','order_by_number')->orderBy('pivot_order_by_number','ASC');
    }

    public function childSitemaps(){
        return $this->hasMany('App\Sitemap','parent_id','id');
        //->where('category','locatie');

        return $this->belongsToMany('App\Category', 'sitemap_category', 'sitemap_id')
            ->withPivot('category','order_by_number')->orderBy('pivot_order_by_number','ASC');

        //return $this->belongsToMany('App\MediaTranslation', 'sitemaptranslation_mediatranslation', 'sitemaptranslation_id','mediatranslation_id')
        //    ->withPivot('field_name','order_by_number')->orderBy('pivot_order_by_number','ASC');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('category',$type)->lists('sitemap_category_id');
    }



    /**
     * Get a list of sitemap_category_id's associated with the current sitemap
     * triggered by method category_list()
     * @return array
     */
    public function getCategoryListAttribute(){
        //dc('getmETHOD');
        //return [];
        return $this->categories->lists('sitemap_category_id')->all();

    }

    public function getCategoryListAttribute1($test){
        return $this->categories()->where('category',$test)->lists('sitemap_category_id')->all();
    }


    //for reselect choosen tab etc
    public static function getViewConfigValue($key){
        if (request()->session()->has($key)){
            return request()->session()->get($key);
        }
        return false;

    }


    public function getCategoryListAttributeTest(){
        //dc('getmETHOD');
        //return [];
        return $this->categories();

    }
    //when locale is enabled after create
    public function setDummyDataForTranslation($sitemap,$enabledLocale)
    {

        $sitemap->translations[$enabledLocale->languageCode] = new SitemapTranslation();

        //used for create() in update()
        //$sitemap->translations[$enabledLocale->languageCode]->post_id = $sitemap->id;
        //$sitemap->translations[$enabledLocale->languageCode]->locale_id = $enabledLocale->id;

        //used to set dummy data in form
        $sitemap->translations[$enabledLocale->languageCode]->name = 'new name, new enabled locale [MODEL]' . $enabledLocale->languageCode . '';
        $sitemap->translations[$enabledLocale->languageCode]->content = 'new content, new enabled locale [MODEL] ' . $enabledLocale->languageCode . '';

        /*
                //post_type
                switch ($sitemap->template->db_table_name) {
                    case "news":
                        $sitemap->translations[$enabledLocale->languageCode]['news'] = [
                            'author' => 1
                            , 'news_test_body' => 'new body, new enabled locale ' . $enabledLocale->languageCode . ''
                        ];
                        break;
                    case "vacancies":
                        $sitemap->translations[$enabledLocale->languageCode]['vacancies'] = [
                            'mail_to' => 'test@test.' . $enabledLocale->languageCode . ''
                            , 'vacancies_test_body' => 'new body, new enabled locale ' . $enabledLocale->languageCode . ''
                        ];
                        break;
                    default:
                }
        */
    }
}
