<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

trait ImageHandler
{
    public string $public_path = "/public/images/";
    public string $storage_path = "/storage/images/";

    /**
     * upload images with this function
     */
    public function uploadImage($file, $path)
    {
        if ( $file ) {
            $extension = $file->getClientOriginalExtension();
            $file_name = $path . DIRECTORY_SEPARATOR . date('Y')
                . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR
                . date('d') . DIRECTORY_SEPARATOR . time() . '.' . $extension;
            $url = $file->storeAs($this->public_path, $file_name);
            $public_path = public_path($this->storage_path . $file_name);
            if (!file_exists($public_path))
                File::makeDirectory($public_path);
            $img = Image::make($public_path);
            $url = preg_replace( "/public/", "", $url);
            return $img->save($public_path) ? $url : '';
        }
    }

    public function deleteImage($imagePath)
    {

    }
}
