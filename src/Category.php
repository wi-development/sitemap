<?php

namespace WI\Sitemap;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'sitemap_category';
	
	public $timestamps = false;
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',
    ];
	
	//Categoru hasMany Sitemap
    public function sitemap()
    {
        return $this->hasMany('App\Sitemap', 'role_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne('App\SitemapTranslation','sitemap_id','sitemap_category_id')->where('locale_id',config('app.locale_id'));
    }
}
