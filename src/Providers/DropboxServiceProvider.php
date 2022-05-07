<?php

namespace Nonoesp\Folio\Providers;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 8.x
        // Storage::extend('dropbox', function ($app, $config) {
        //     $client = new DropboxClient(
        //         $config['authorization_token']
        //     );

        //     return new Filesystem(new DropboxAdapter($client));
        // });

        // 9x
        Storage::extend('dropbox', function ($app, $config) {
            $adapter = new DropboxAdapter(
                new DropboxClient($config['authorization_token'])
            );
         
            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}