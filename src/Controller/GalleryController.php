<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Form\GalleryType;
use App\Form\PhotoType;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class GalleryController extends AbstractController
{
    #[Route('/', name: 'app_gallery')]
    public function index(GalleryRepository $galleryRepository): Response
    {
        $galleries = $galleryRepository->findAll();

        return $this->render('gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'galleries' => $galleries
        ]);
    }

    #[Route('/gallery/{id}', name: 'app_gallery_show', requirements: ['id' => '\d+'])]
    public function show(GalleryRepository $galleryRepository, $id): Response
    {
        $gallery = $galleryRepository->find($id);

        return $this->render('gallery/show.html.twig', [
            'controller_name' => 'GalleryController',
            'gallery' => $gallery
        ]);
    }

    #[Route('/gallery/create', name: 'app_gallery_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $gallery = $form->getData();
            $gallery->setCreatedAt(new \DateTimeImmutable());
            $gallery->setUpdatedAt(new \DateTimeImmutable());
            $image = $form->get('background')->getData();
            $fileSystem = new Filesystem();
            $gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                if ($fileSystem->exists($gallery_directory)) {
                    throw new \Exception('Une gallerie existe déjà avec ce nom !');
                }

                try {
                    $fileSystem->mkdir($gallery_directory);
                    $image->move(
                        $gallery_directory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Une erreur est survenue lors de l\'upload de l\'image !');
                }

                $gallery->setBackgroundPath($newFilename);
            }
            $entityManager->persist($gallery);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_gallery');
        }

        return $this->render('gallery/create.html.twig', [
            'controller_name' => 'GalleryController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/gallery/edit/{id}', name: 'app_gallery_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, GalleryRepository $galleryRepository, $id): Response
    {
        $fileSystem = new Filesystem();
        //Get current gallery and pass it to the form
        $gallery = $galleryRepository->find($id);
        $oldGalleryName = $gallery->getName();
        $form = $this->createForm(GalleryType::class, $gallery);




        $form->handleRequest($request);

        //Form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $gallery = $form->getData();
            $gallery->setUpdatedAt(new \DateTimeImmutable());
            $image = $form->get('background')->getData();
            //Rename gallery directory if name has changed
            if ($oldGalleryName !== $gallery->getName()) {
                $gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($oldGalleryName));
                $new_gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));
                $fileSystem->rename($gallery_directory, $new_gallery_directory);
            }

            $new_gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));

            //Upload new image if there is one
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                //Delete old image if there is one
                if ($gallery->getBackgroundPath() != null && $fileSystem->exists($new_gallery_directory . '/' . $gallery->getBackgroundPath())) {
                    $fileSystem->remove($new_gallery_directory . '/' . $gallery->getBackgroundPath());
                }
                //Upload new image
                try {
                    $image->move(
                        $new_gallery_directory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Une erreur est survenue lors de l\'upload de l\'image !');
                }
                $gallery->setBackgroundPath($newFilename);
            } else {
                $gallery->setBackgroundPath($gallery->getBackgroundPath());
            }

            $entityManager->persist($gallery);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_gallery');
        }

        return $this->render('gallery/edit.html.twig', [
            'controller_name' => 'GalleryController',
            'form' => $form->createView(),
            'gallery' => $gallery
        ]);
    }

    #[Route('/gallery/delete/{id}', name: 'app_gallery_delete')]
    public function delete(EntityManagerInterface $entityManager, GalleryRepository $galleryRepository, $id): Response
    {
        $gallery = $galleryRepository->find($id);
        $entityManager->remove($gallery);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_gallery');
    }

    #[Route('/gallery/{id}/photo/create', name: 'app_gallery_photo_create')]
    public function createPhoto(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, GalleryRepository $galleryRepository, $id): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $gallery = $galleryRepository->find($id);
        $form->remove('gallery');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->getData();
            $photo->setCreatedAt(new \DateTimeImmutable());
            $photo->setUpdatedAt(new \DateTimeImmutable());
            $image = $form->get('img')->getData();
            $fileSystem = new Filesystem();
            $gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));
            if (!$image) {
                throw new \Exception('Vous devez ajouter une image !');
            }
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

            if (!$fileSystem->exists($gallery_directory)) {
                throw new \Exception('Aucune gallerie n\'existe avec ce nom !');
            }

            try {
                $image->move(
                    $gallery_directory,
                    $newFilename
                );
            } catch (FileException $e) {
                throw new \Exception('Une erreur est survenue lors de l\'upload de l\'image !');
            }

            $photo->setPath($newFilename);

            $photo->setGallery($gallery);
            $entityManager->persist($photo);
            $entityManager->flush();
            return $this->redirectToRoute('app_gallery_show', ['id' => $gallery->getId()]);
        }




        return $this->render('gallery/create_photo.html.twig', [
            'controller_name' => 'GalleryController',
            'form' => $form->createView()
        ]);
    }
}
