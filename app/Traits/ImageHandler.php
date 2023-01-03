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
    public function uploadImage($request, $path, string $inputName = 'avatar')
    {
        $requestFile = $request->file($inputName);
        try {
            if ($requestFile)
            {
                $extension = $requestFile->getClientOriginalExtension();
                $file_name = date('Y')
                    . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR
                    . date('d') . DIRECTORY_SEPARATOR . time() . '.' . $extension;
                $fix_name = $file_name;
                $pathAddress = $requestFile->storeAs($path, $fix_name, 'public');
                $fixAddress = str_replace('\\', '/', $pathAddress);
                $final_path = '/' . $fixAddress;
                Image::make($requestFile->getRealPath());
                return $final_path;
            }
            return true;
        } catch (\Exception $exception)
        {
            return $exception->getMessage();
        }
    }

    public function checkImage($file)
    {
        try {
            if ($file)
            {
                $this->deleteImage($file);
            }
            return true;
        } catch (\Exception $exception)
        {
            return $exception->getMessage();
        }
    }

    public function deleteImage($file)
    {
        try {
            if ($file)
            {
                Storage::delete('public' . $file);
            }
            return true;
        } catch (\Exception $exception)
        {
            report($exception);
            return $exception->getMessage();
        }
    }
}
