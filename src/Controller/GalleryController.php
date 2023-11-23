<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
