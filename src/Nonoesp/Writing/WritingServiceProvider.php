<?php namespace Nonoesp\Writing;

use Illuminate\Support\ServiceProvider;

class WritingServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('nonoesp/writing');

    	include __DIR__.'/routes.php';		
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->booting(function()
		{
		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
		  $loader->alias('Writing', 'Nonoesp\Writing\Facades\Writing');
		});

		$this->app['writing'] = $this->app->share(function($app)
		{
		return new Writing;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('writing');
	}

}
