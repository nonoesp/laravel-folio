<?php

namespace Nonoesp\Folio\Providers;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Storage;

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
        Storage::extend('digitalocean', function ($app, $config) {
            $client = new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => 'latest|version',
                'endpoint' => 'https://your-region.digitaloceanspaces.com',
            ]);

            $adapter = new AwsS3Adapter($client, $config['bucket']);

            return new Filesystem($adapter);
        });
    }
}