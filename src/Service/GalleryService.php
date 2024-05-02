<?php

namespace App\Service;

use App\Entity\Gallery;
use App\Util\FileUtil;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class GalleryService
{

    public function __construct(private FileUtil $fileUtil, private string $galleryImagesDirectory, private Filesystem $fileSystem)
    {
    }

    public function saveBackgroundImgFile(Gallery $gallery, File $backgroundImgFile): string
    {

        $galleryDirectory = $this->galleryImagesDirectory . str_replace(' ', '_', strtolower($gallery->getName()));

        if ($this->fileSystem->exists($galleryDirectory)) {
            throw new Exception("There is already a directory with the gallery name !");
        }
        $this->fileSystem->mkdir($galleryDirectory);

        return $this->fileUtil->uploadFile($backgroundImgFile, $galleryDirectory);
    }

    public function renameGalleryDirectory(string $oldGalleryName, string $newGalleryName): string
    {
        $galleryDirectory = $this->galleryImagesDirectory . str_replace(' ', '_', strtolower($oldGalleryName));
        $newGalleryDirectory = $this->galleryImagesDirectory . str_replace(' ', '_', strtolower($newGalleryName));

        $this->fileSystem->rename($galleryDirectory, $newGalleryDirectory);

        return $newGalleryDirectory;
    }
}
