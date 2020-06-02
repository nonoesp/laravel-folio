<?php namespace Nonoesp\Folio;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

class FolioServiceProvider extends ServiceProvider
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
        $path_build = __DIR__.'/../resources/build';
        $path_sass = __DIR__.'/../resources/sass';
        $path_js = __DIR__.'/../resources/js';
        $path_migrations = __DIR__.'/../migrations';
        $path_config = __DIR__.'/../config/config.php';

        // Publish Paths
        $publish_path_views = base_path('resources/views/nonoesp/folio');
        $publish_path_lang = base_path('resources/lang/nonoesp/folio');
        $publish_path_middleware = base_path('app/Http/Middleware');
        $publish_path_build = base_path('public/');
        $publish_path_sass = base_path('resources/sass');
        $publish_path_js = base_path('resources/js');
        $publish_path_config = config_path('folio.php');

        // Publish Stuff
        $this->publishes([$path_views => $publish_path_views,], 'views');
        $this->publishes([$path_lang => $publish_path_lang,], 'lang');
        $this->publishes([$path_middleware => $publish_path_middleware,], 'middleware');
        $this->publishes([$path_build => $publish_path_build,], 'build');
        $this->publishes([$path_sass => $publish_path_sass,], 'dev-assets');//'sass');
        $this->publishes([$path_js => $publish_path_js,], 'dev-assets');//'js');
        $this->publishes([$path_config => $publish_path_config,], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'folio');

        // Views
        if (is_dir($publish_path_views)) {
            $this->loadViewsFrom($publish_path_views, 'folio'); // Load published views
        } else {
            $this->loadViewsFrom($path_views, 'folio');
        }

        // Translations
        if (is_dir($publish_path_lang)) {
            $this->loadTranslationsFrom($publish_path_lang, 'folio'); // Load published lang
        } else {
            $this->loadTranslationsFrom($path_lang, 'folio');
        }

        $this->loadMigrationsFrom($path_migrations);

        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Nonoesp\Folio\Commands\GenerateSitemap::class,
                \Nonoesp\Folio\Commands\MigrateTemplate::class,
                \Nonoesp\Folio\Commands\TextAndTitleToJSON::class,
                \Nonoesp\Folio\Commands\ItemPropertiesExport::class,
                \Nonoesp\Folio\Commands\ItemPropertiesImport::class,
                \Nonoesp\Folio\Commands\ItemRetag::class,
                \Nonoesp\Folio\Commands\ItemClone::class,
                \Nonoesp\Folio\Commands\InstallCommand::class,
                \Nonoesp\Folio\Commands\CreateUserCommand::class,
            ]);
        }

        $this->commands([
            \Nonoesp\Folio\Commands\ItemClone::class,
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register Controllers
        //$this->app->make('Nonoesp\Folio\Controllers\Controller');
        //$this->app->make('Nonoesp\Folio\Controllers\FolioController');

        // Merge Config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'folio');

        include __DIR__.'/routes.php';

        // Create alias
        $this->app->booting(function()
        {
          $loader = \Illuminate\Foundation\AliasLoader::getInstance();
          $loader->alias('Folio', 'Nonoesp\Folio\Facades\Folio');
        });

        $this->app->singleton('folio', function (Container $app) {
             return new Folio();
         });
        $this->app->alias('folio', Folio::class);

    }
}
