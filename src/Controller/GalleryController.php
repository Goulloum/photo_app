<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryType;
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

    #[Route('/gallery/{id}', name: 'app_gallery_show')]
    public function show(GalleryRepository $galleryRepository, $id): Response
    {
        $gallery = $galleryRepository->find($id);

        return $this->render('gallery/show.html.twig', [
            'controller_name' => 'GalleryController',
            'gallery' => $gallery
        ]);
    }

    #[Route('/admin/gallery/create', name: 'app_gallery_create')]
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
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                $gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));
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

    #[Route('/admin/gallery/edit/{id}', name: 'app_gallery_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, GalleryRepository $galleryRepository, $id): Response
    {
        $fileSystem = new Filesystem();

        $gallery = $galleryRepository->find($id);
        $form = $this->createForm(GalleryType::class, $gallery);

        $backgroundImage = new File($this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName())) . '/' . $gallery->getBackgroundPath());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $gallery = $form->getData();
            $gallery->setUpdatedAt(new \DateTimeImmutable());
            $image = $form->get('background')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                $gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));
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

        return $this->render('gallery/edit.html.twig', [
            'controller_name' => 'GalleryController',
            'form' => $form->createView(),
            'backgroundImage' => $backgroundImage
        ]);
    }

    #[Route('/admin/gallery/delete/{id}', name: 'app_gallery_delete')]
    public function delete(EntityManagerInterface $entityManager, GalleryRepository $galleryRepository, $id): Response
    {
        $gallery = $galleryRepository->find($id);
        $entityManager->remove($gallery);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_gallery');
    }
}
