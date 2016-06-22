<?php

namespace WI\Sitemap;


use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class SitemapServiceProvider extends ServiceProvider
{


	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	#protected $defer = true;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if (is_dir(base_path() . '/resources/views/wi/sitemap')) {
			$this->loadViewsFrom(base_path() . '/resources/views/wi/sitemap', 'sitemap');

		} else {
			$this->loadViewsFrom(__DIR__.'/views', 'sitemap');
		}
		if (!$this->app->routesAreCached()) {
			$this->setupRoutes($this->app->router);
		}

		config([
			'config/sitemap.php',
		]);


		$this->publishes([
			__DIR__.'/views' => base_path('resources/views/wi/sitemap'),
			__DIR__.'/config/sitemap.php' => config_path('sitemap.php')
		]);
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function setupRoutes(Router $router)
	{
		$router->group([
			//'namespace' => 'WI\Sitemap',
			'namespace' => 'WI\Sitemap',	// Controllers Within The "WI\Sitemap" Namespace
			'as' => 'admin::',		// Route named "admin::
			//'prefix' => 'backStage',	// Matches The "/admin" URL
			'prefix' => config('wi.dashboard.admin_prefix'),
			'middleware' => ['web','auth']	// Use Auth Middleware
		],
			function($router)
			{
				require __DIR__.'/routes.php';
			}
		);

	}

	/**
	 * Register the application services.
	 * https://laracasts.com/discuss/channels/general-discussion/how-to-move-my-controllers-into-a-seperate-package-folder
	 * @return void
	 */
	public function register()
	{
		#dd('asdf');
		#include __DIR__.'/routes.php';
		//$this->app->make('WI\Sitemap\SitemapController');

		//$this->app->register(Vendor\Package\Providers\RouteServiceProvider::class);

		$this->app->bind(
			'WI\Sitemap\Repositories\SitemapRepositoryInterface',
			'WI\Sitemap\Repositories\DbSitemapRepository'
		);
		/*
		$this->app->bind(
			'Repositories\Reference\DbReferenceRepository',
			function () {
				$repository = new EloquentPageRepository(new Page());
				return $repository;
				if (! Config::get('app.cache')) {
					return $repository;
				}

				return new CachePageDecorator($repository);

			}
		);
*/


	}
}
