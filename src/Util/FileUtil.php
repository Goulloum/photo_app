<?php

namespace App\Util;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class FileUtil
{

    /**
     * Upload a file to a directory
     *
     * @param File $file
     * @param string $directory
     * @return string The uploaded file name
     */
    final public function uploadFile(File $file, string $directory): string
    {
        $fileName = $file->getFilename() . '_' . uniqid() . '.' . $file->guessExtension();

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
