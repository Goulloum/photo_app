<?php

namespace App\Util;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUtil
{

    public function __construct(private SluggerInterface $slugger)
    {
    }

    /**
     * Upload a file to a directory
     *
     * @param File $file
     * @param string $directory
     * @return string The uploaded file name
     */
    final public function uploadFile(File $file, string $directory): string
    {
        $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '_' . uniqid() . '.' . $file->guessExtension();

        $file->move($directory, $fileName);

        return $fileName;
    }

    final public function removeFile(string $path): void
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($path)) {
            $fileSystem->remove($path);
        }
    }
}
