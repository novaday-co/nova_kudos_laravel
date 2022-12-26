<?php

namespace App\Services\Image;

use Intervention\Image\Facades\Image;

class ImageService extends ImageTools
{
    public function save($image)
    {
        // set image
        $this->setImage($image);
        // execute provider
        $this->provider();
        // save image
        $res = Image::make($image->getRealPath())->save(storage_path($this->getImageAddress()), null, $this->getImageFormat());
        return $res ? $this->getImageAddress() : false;
    }

    public function fitAndSave($image, $width, $height)
    {
        //set image
        $this->setImage($image);
        //execute provider
        $this->provider();
        //save image
        $res = Image::make($image->getRealPath())->fit($width, $height)->save(storage_path($this->getImageAddress()), null, $this->getImageFormat());
        return $res ? $this->getImageAddress() : false;
    }

    public function deleteImage($imagePath)
    {
        if (file_exists($imagePath))
            unlink($imagePath);
    }
}
