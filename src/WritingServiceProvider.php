<?php namespace Nonoesp\Writing;

use Illuminate\Support\ServiceProvider;

class WritingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Paths
        $path_views = __DIR__.'/../resources/views';
        $path_lang = __DIR__.'/../resources/lang';
        $path_middleware = __DIR__.'/Middleware';
        $path_assets = __DIR__.'/../resources/assets';

        // Publish Paths
        $publish_path_views = base_path('resources/views/nonoesp/writing');
        $publish_path_lang = base_path('resources/lang/nonoesp/writing');
        $publish_path_middleware = base_path('app/Http/Middleware');
        $publish_path_assets = base_path('public/nonoesp/writing');
        $publish_path_config = config_path('writing.php');

        // Publish Stuff
        $this->publishes([$path_views => $publish_path_views,], 'views');
        $this->publishes([$path_lang => $publish_path_lang,], 'lang');
        //$this->publishes([$path_middleware => $publish_path_middleware,], 'middleware');
        $this->publishes([$path_assets => $publish_path_assets,], 'assets');
        $this->publishes([__DIR__.'/../config/config.php' => $publish_path_config,], 'config');

        // Views
        if (is_dir($publish_path_views)) {
            $this->loadViewsFrom($publish_path_views, 'writing'); // Load published views
        } else {
            $this->loadViewsFrom($path_views, 'writing');
        }

        // Translations
        if (is_dir($publish_path_lang)) {
            $this->loadTranslationsFrom($publish_path_lang, 'writing'); // Load published lang
        } else {
            $this->loadTranslationsFrom($path_lang, 'writing');
        }  
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register Controller
        //$this->app->make('Nonoesp\Writing\WritingController');

        // Merge Config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'writing');

        include __DIR__.'/routes.php';

        // Create alias
        $this->app->booting(function()
        {
          $loader = \Illuminate\Foundation\AliasLoader::getInstance();
          $loader->alias('Writing', 'Nonoesp\Writing\Facades\Writing');
        });

        // Return alias
        $this->app['writing'] = $this->app->share(function($app)
        {
        return new Writing;
        });
    }
}
