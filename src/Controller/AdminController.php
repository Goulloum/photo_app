<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\GalleryRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(): Response
    {


        return $this->redirectToRoute('app_admin_gallery');
    }


    #[Route('/gallery', name: 'gallery')]
    public function gallery(GalleryRepository $galleryRepository): Response
    {
        $galleries = $galleryRepository->findAll();
        return $this->render('admin/gallery.html.twig', [
            'galleries' => $galleries,
        ]);
    }

    #[Route('/gallery/{id}', name: 'gallery_show', requirements: ['id' => '\d+'])]
    public function galleryShow(GalleryRepository $galleryRepository, $id): Response
    {
        $gallery = $galleryRepository->find($id);
        return $this->render('admin/gallery_show.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/user', name: 'user')]
    public function user(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user.html.twig', ['users' => $users]);
    }

    #[Route('/event', name: 'event')]
    public function event(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->render('admin/event.html.twig',
            ['events' => $events]);
    }


}
