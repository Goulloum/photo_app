<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {


        return $this->redirectToRoute('app_admin_gallery');
    }


    #[Route('/admin/gallery', name: 'app_admin_gallery')]
    public function gallery(GalleryRepository $galleryRepository): Response
    {
        $galleries = $galleryRepository->findAll();
        return $this->render('admin/gallery.html.twig', [
            'galleries' => $galleries,
        ]);
    }

    #[Route('/admin/gallery/{id}', name: 'app_admin_gallery_show', requirements: ['id' => '\d+'])]
    public function galleryShow(GalleryRepository $galleryRepository, $id): Response
    {
        $gallery = $galleryRepository->find($id);
        return $this->render('admin/gallery_show.html.twig', [
            'gallery' => $gallery,
        ]);
    }
}
