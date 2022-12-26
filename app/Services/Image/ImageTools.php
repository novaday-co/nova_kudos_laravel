<?php

namespace App\Services\Image;

class ImageTools
{
    protected $image;
    protected $customDirectory;
    protected $imageDirectory;
    protected $imageName;
    protected $imageFormat;
    protected $finalImageName;
    protected $finalImageDirectory;

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getCustomDirectory()
    {
        return $this->customDirectory;
    }

    public function setCustomDirectory(string $customDirectory)
    {
        $this->customDirectory = trim($customDirectory, '/\\');
    }

    public function getImageDirectory()
    {
        return $this->imageDirectory;
    }

    public function setImageDirectory(string $imageDirectory)
    {
        $this->imageDirectory = trim($imageDirectory, '/\\');
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName)
    {
        $this->imageName = $imageName;
    }

    public function setCurrentImageName()
    {
        return !empty($this->image) ? $this->setImageName(pathinfo($this->image->getClientOrginalName(), PATHINFO_FILENAME)) : false;
    }

    public function getImageFormat()
    {
        return $this->imageFormat;
    }

    public function setImageFormat($imageFormat)
    {
        $this->imageFormat = $imageFormat;
    }

    protected function checkDirectory($imageDirectory)
    {
        if (!file_exists($imageDirectory))
        {
            mkdir($imageDirectory, 0755, true);
        }
    }

    public function getFinalImageDirectory()
    {
        return $this->finalImageDirectory;
    }

    public function setFinalImageDirectory($finalImageDirectory)
    {
        $this->finalImageDirectory = $finalImageDirectory;
    }

    public function getFinalImageName()
    {
        return $this->finalImageName;
    }

    public function setFinalImageName($finalImageName)
    {
        $this->finalImageName = $finalImageName;
    }

    public function getImageAddress()
    {
        return $this->finalImageDirectory . DIRECTORY_SEPARATOR . $this->finalImageName;
    }

    protected function provider()
    {
        //set properties
        $this->getImageDirectory() ?? $this->setImageDirectory(date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d'));
        $this->getImageName() ?? $this->setImageName(time());
        $this->getImageFormat() ?? $this->setImageFormat($this->image->extension());


        //set final image Directory
        $finalImageDirectory = empty($this->getCustomDirectory()) ? $this->getImageDirectory() : $this->getCustomDirectory() . DIRECTORY_SEPARATOR . $this->getImageDirectory();
        $this->setFinalImageDirectory($finalImageDirectory);


        //set final image name
        $this->setFinalImageName($this->getImageName() . '.' . $this->getImageFormat());


        //check create final image directory
        $this->checkDirectory($this->getFinalImageDirectory());
    }
}
