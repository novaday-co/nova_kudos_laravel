<?php

namespace App\Http\Services\Image;

class ImageUploader extends ImageService
{
    public function create($request, string $file, string $directory)
    {
        // check request for upload image
        if ($request->hasFile($file)) {
            $this->setCustomDirectory('images' . DIRECTORY_SEPARATOR . $directory);
            $result = $this->save($request->file($directory));
            // check upload
            if ($result === false)
                return response('error uploading photo ', 400);
            return $result;
        }
    }
}
