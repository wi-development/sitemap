<?php
/*
|--------------------------------------------------------------------------
| Sitemap Routes roles = Administrator
|--------------------------------------------------------------------------
|
*/

//test ignore

//index (all)
Route::get('/sitemap/all/data/{sitemap_parent_id?}',        ['as' => 'sitemap.index.all.data',      'uses' => 'SitemapController@indexAllData']);
Route::get('/sitemap/all/{sitemap_parent_id?}',             ['as' => 'sitemap.index.all',           'uses' => 'SitemapController@getIndexAll']);

Route::get('/sitemap/news',                                 ['as' => 'sitemap.news.index',          'uses' => 'SitemapController@getNewsIndex']);
Route::get('/sitemap/news/data',                            ['as' => 'sitemap.news.data',           'uses' => 'SitemapController@newsIndexData']);

Route::get('/sitemap/locaties/data/{sitemap_parent_id?}',   ['as' => 'sitemap.locaties.index.data',         'uses' => 'SitemapController@indexLocatiesData']);
Route::get('/sitemap/locaties/{sitemap_parent_id?}',        ['as' => 'sitemap.locaties.index',              'uses' => 'SitemapController@getIndexLocaties']);

Route::get('/sitemap/locaties_sub/data/{sitemap_parent_id?}',
															['as' => 'sitemap.locaties_sub.data'        ,'uses' => 'SitemapController@indexLocatiesSubData']);
Route::get('/sitemap/locaties_sub/{sitemap_parent_id?}',    ['as' => 'sitemap.locaties_sub.index'       ,'uses' => 'SitemapController@getIndexLocatiesSub']);


//edit form
Route::get('/sitemap/{id}/edit',                            ['as' => 'sitemap.edit'                 ,'uses' => 'SitemapController@edit']);
	//??
	//route('admin::sitemap.edit')
	//Route::get('/sitemap/{post_type}/{id}/edit', 'SitemapController@edit');


//add form
Route::get('/sitemap/create',                               ['as' => 'sitemap.create'               ,'uses' => 'SitemapController@selectSitemapTypeBeforeCreate']);
//??
//Route::get('/sitemap/{template_slug}/create',
//	['as' => 'sitemap.template.create'  ,'uses' => 'SitemapController@create']);


//database
Route::post('/sitemap',                                     ['as' => 'sitemap.store'                ,'uses' => 'SitemapController@store']);
Route::post('/sitemap/sort',                                ['as' => 'sitemap.sort'                 ,'uses' => 'SitemapController@sort']);
Route::post('/sitemap/duplicate/{sitemap_id?}',             ['as' => 'sitemap.duplicate'            ,'uses' => 'SitemapController@duplicate']);
Route::patch('/sitemap/{sitemap}',
	[
		'as' => 'sitemap.update',
		'uses' => 'SitemapController@update'
		//'middleware' => ['roles'],
		//'roles' => ['administrator']
	]
);

//sitemap delete ONLY ROOT
Route::delete('sitemap/{id?}', //? is voor ajax
	[
		'as' => 'sitemap.delete',
		'uses' => 'SitemapController@destroy'
		//'middleware' => ['roles'],
		//'roles' => ['root'] // Only an root can delete from database
]);


//session
//Route::post('/sitemap/session',                         ['as' => 'sitemap.session'      ,'uses' => 'SitemapController@storeSession']);


/* NEW */
/*
//index
Route::get('/sitemap',                  ['as' => 'sitemap.index'            ,'uses' => 'SitemapController@index']);

//Route::get('/newsx',                     ['as' => 'sitemap.news'   ,'uses' => 'SitemapController@newsIndex']);



Route::get('/sitemap/locaties/data/{sitemap_parent_id?}',                ['as' => 'locaties.index.data'      ,'uses' => 'SitemapController@indexLocatiesData']);
Route::get('/sitemap/locaties/{sitemap_parent_id?}',                     ['as' => 'locaties.index'           ,'uses' => 'SitemapController@getIndexLocaties']);

Route::get('/sitemap/locaties_sub/data/{sitemap_parent_id?}',
['as' => 'locaties_sub.data'        ,'uses' => 'SitemapController@indexLocatiesSubData']);
Route::get('/sitemap/locaties_sub/{sitemap_parent_id?}',                 ['as' => 'locaties_sub.index'       ,'uses' => 'SitemapController@getIndexLocatiesSub']);




Route::get('/sitemap/news',                         ['as' => 'news.index'               ,'uses' => 'SitemapController@getNewsIndex']);
Route::get('/sitemap/news/data',                    ['as' => 'news.data'                ,'uses' => 'SitemapController@newsIndexData']);

Route::get('/sitemap/events',                         ['as' => 'events.index'               ,'uses' => 'SitemapController@getEventsIndex']);
Route::get('/sitemap/events/data',                    ['as' => 'events.data'                ,'uses' => 'SitemapController@eventsIndexData']);


Route::get('/sitemap/all/data/{sitemap_parent_id?}',       ['as' => 'sitemap.index.all.data'  ,'uses' => 'SitemapController@indexAllData']);
Route::get('/sitemap/all/{sitemap_parent_id?}',            ['as' => 'sitemap.index.all'       ,'uses' => 'SitemapController@getIndexAll']);



//Route::get('/sitemapTable/{parent_id?}',             ['as' => 'sitemap.indexDataTable'   ,'uses' => 'SitemapController@indexDataTable']);
//Route::get('/sitemap/{post_type}', 'SitemapController@index');


//edit form
//Route::get('/sitemap/{id}/edit', 'SitemapController@edit');
Route::get('/sitemap/{id}/edit',                  ['as' => 'sitemap.edit'            ,'uses' => 'SitemapController@edit']);
//route('admin::sitemap.edit')
Route::get('/sitemap/{post_type}/{id}/edit', 'SitemapController@edit');


//add form
Route::get('/sitemap/create',           ['as' => 'sitemap.create'           ,'uses' => 'SitemapController@selectSitemapTypeBeforeCreate']);
Route::get('/sitemap/{template_slug}/create',
['as' => 'sitemap.template.create'  ,'uses' => 'SitemapController@create']);


//indexNav
//Route::get('/sitemap/nav/{parent_sitemap_id?}', ['as' => 'sitemap.indexNav'
//    ,'uses' => 'SitemapController@indexNav']);
//index
//Route::get('/sitemap/{post_container}', ['as' => 'sitemap.postcontainer.index'
//    ,'uses' => 'SitemapController@indexPosts']);




//database
Route::post('/sitemap',                         ['as' => 'sitemap.store'                ,'uses' => 'SitemapController@store']);
Route::post('/sitemap/sort',                    ['as' => 'sitemap.sort'                 ,'uses' => 'SitemapController@sort']);
Route::post('/sitemap/duplicate/{sitemap_id?}',  ['as' => 'sitemap.duplicate'            ,'uses' => 'SitemapController@duplicate']);
Route::patch('/sitemap/{sitemap}',

[
'middleware' => ['auth', 'roles'],
'as' => 'sitemap.update' ,'uses' => 'SitemapController@update',
'roles' => ['administrator']

]);

//sitemap delete ONLY ROOT
Route::delete('sitemap/{id?}', [  //? is voor ajax
'middleware' => ['auth', 'roles'],
'uses' => 'SitemapController@destroy',
'as' => 'sitemap.delete',
'roles' => ['root'] // Only an root can delete from database
]);
//Route::get('/sitemap/select', 'SitemapController@selectSitemapTypeBeforeCreate');//MOET WEG

//session
Route::post('/sitemap/session',                         ['as' => 'sitemap.session'      ,'uses' => 'SitemapController@storeSession']);
*/

