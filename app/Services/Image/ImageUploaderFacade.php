<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\Facade;

class ImageUploaderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imageuploader';
    }
}
