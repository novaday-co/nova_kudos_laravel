<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImageHandler
{
    /**
     * upload images with this function
     */
    public function uploadImage($file, $path)
    {
        if ($file)
        {
            $extension = $file->getClientOriginalExtension();
            $file_name = date('Y')
                . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR
                . date('d') . DIRECTORY_SEPARATOR . time() . '.' . $extension;
            $pathAddress = $file->storeAs($path, $file_name, 'public');
            $final_path = '/' . $pathAddress;
            Image::make($file->getRealPath());
            return $final_path;
       }
}
}
