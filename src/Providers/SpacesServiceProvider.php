<?php

namespace Nonoesp\Folio\Providers;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

/**
 * Digital Ocean Spaces
 */
class SpacesServiceProvider extends ServiceProvider
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
        // Storage::extend('spaces', function ($app, $config) {
        //     $client = new S3Client([
        //         'credentials' => [
        //             'key'    => $config['key'],
        //             'secret' => $config['secret'],
        //         ],
        //         'region' => $config['region'],
        //         'version' => 'latest',
        //         'endpoint' => $config['endpoint'],
        //     ]);

        //     $adapter = new AwsS3Adapter($client, $config['bucket']);

        //     return new Filesystem($adapter);
        // });

        // 9.x
        Storage::extend('spaces', function ($app, $config) {
            $client = new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => 'latest',
                'endpoint' => $config['endpoint'],
            ]);

            $adapter = new AwsS3V3Adapter($client, $config['bucket']);
         
            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}