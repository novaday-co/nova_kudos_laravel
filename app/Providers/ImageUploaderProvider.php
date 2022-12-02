<?php

namespace App\Providers;

use App\Http\Services\Image\ImageUploader;
use Illuminate\Support\ServiceProvider;

class ImageUploaderProvider extends ServiceProvider
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
        $this->app->bind('imageuploader', function (){
            return new ImageUploader();
        });
    }
}
