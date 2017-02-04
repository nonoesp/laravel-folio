<?php namespace Nonoesp\Space;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

class SpaceServiceProvider extends ServiceProvider
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
        $path_sass = __DIR__.'/../resources/assets-dev/sass';
        $path_js = __DIR__.'/../resources/assets-dev/js';
        $path_migrations = __DIR__.'/../migrations';

        // Publish Paths
        $publish_path_views = base_path('resources/views/nonoesp/space');
        $publish_path_lang = base_path('resources/lang/nonoesp/space');
        $publish_path_middleware = base_path('app/Http/Middleware');
        $publish_path_assets = base_path('public/nonoesp/space');
        $publish_path_sass = base_path('resources/assets/sass');
        $publish_path_js = base_path('resources/assets/js');
        $publish_path_config = config_path('space.php');

        // Publish Stuff
        $this->publishes([$path_views => $publish_path_views,], 'views');
        $this->publishes([$path_lang => $publish_path_lang,], 'lang');
        $this->publishes([$path_middleware => $publish_path_middleware,], 'middleware');
        $this->publishes([$path_assets => $publish_path_assets,], 'assets');
        $this->publishes([$path_sass => $publish_path_sass,], 'dev-assets');//'sass');
        $this->publishes([$path_js => $publish_path_js,], 'dev-assets');//'js');
        $this->publishes([__DIR__.'/../config/config.php' => $publish_path_config,], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'space');


        // Views
        if (is_dir($publish_path_views)) {
            $this->loadViewsFrom($publish_path_views, 'space'); // Load published views
        } else {
            $this->loadViewsFrom($path_views, 'space');
        }

        // Translations
        if (is_dir($publish_path_lang)) {
            $this->loadTranslationsFrom($publish_path_lang, 'space'); // Load published lang
        } else {
            $this->loadTranslationsFrom($path_lang, 'space');
        }

        $this->loadMigrationsFrom($path_migrations);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register Controllers
        //$this->app->make('Nonoesp\Space\Controllers\Controller');
        //$this->app->make('Nonoesp\Space\Controllers\SpaceController');

        // Merge Config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'space');

        include __DIR__.'/routes.php';

        // Create alias
        $this->app->booting(function()
        {
          $loader = \Illuminate\Foundation\AliasLoader::getInstance();
          $loader->alias('Space', 'Nonoesp\Space\Facades\Space');
        });

        $this->app->singleton('space', function (Container $app) {
             return new Space();
         });
        $this->app->alias('space', Space::class);

    }
}
