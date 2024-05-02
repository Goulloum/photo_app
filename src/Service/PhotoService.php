<?php

namespace App\Service;

use App\DTO\UpdatePhotoDTO;
use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PhotoService
{

    private $photoRepository;
    private $gallery_images_directory;
    private $entityManagerInterface;

    public function __construct(PhotoRepository $photoRepository, string $gallery_images_directory, EntityManagerInterface $entityManagerInterface)
    {
        $this->photoRepository = $photoRepository;
        $this->gallery_images_directory = $gallery_images_directory;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function edit(int $id, UpdatePhotoDTO $new_photo): Photo
    {
        //Retrieve the photo from the database
        $photo = $this->photoRepository->find($id);
        if (!$photo) {
            throw new NotFoundHttpException('Photo non trouvée !');
        }

        //Check for each field if it's defined and not blank in the new photo and update it 
        if ($new_photo->name) {
            $photo->setName($new_photo->name);
        }

        if ($new_photo->description) {
            $photo->setDescription($new_photo->description);
        }

        if ($new_photo->location) {
            $photo->setLocation($new_photo->location);
        }

        if ($new_photo->datePhoto) {
            $photo->setDatePhoto($new_photo->datePhoto);
        }

        //Save the changes in the database
        $this->entityManagerInterface->flush();
        return $photo;
    }



    public function delete($id): void
    {
        //Retrieve the photo from the database
        $photo = $this->photoRepository->find($id);
        if (!$photo) {
            throw new NotFoundHttpException('Photo non trouvée !');
        }

        //Get the gallery directory
        $gallery_directory = $this->gallery_images_directory . str_replace(' ', '_', strtolower($photo->getGallery()->getName()));

        $fileSystem = new Filesystem();
        if ($fileSystem->exists($gallery_directory . '/' . $photo->getPath())) {
            $fileSystem->remove($gallery_directory . '/' . $photo->getPath());
        } else {
            throw new NotFoundHttpException('Fichier non trouvé !');
        }

        //Delete the photo from the database
        $this->entityManagerInterface->remove($photo);
        $this->entityManagerInterface->flush();
    }
}
