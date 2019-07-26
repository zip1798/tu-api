<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Storage;

class S3SpacesStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('s3-spaces', function ($app, $config) {
            $client = new S3Client([
                'credentials' => [
                    'key'    => $config["key"],
                    'secret' => $config["secret"]
                ],
                'region'      => $config["region"],
                'version'     => "latest",
                'bucket_endpoint' => false,
                'use_path_style_endpoint' => true,
                'endpoint'    => $config["endpoint"],
            ]);
            return new Filesystem(new AwsS3Adapter($client, $config["bucket"]));
        });
    }
}
