<?php

namespace App\Controller;

use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/photo', name: 'app_photo_')]
class PhotoController extends AbstractController
{

    public function __construct(private PhotoRepository $photoRepository, private EntityManagerInterface $entityManagerInterface, private SluggerInterface $slugger)
    {
    }


    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request): Response
    {
        $photo = $this->photoRepository->find($request->get('id'));
        $form = $this->createForm(PhotoType::class, $photo);
        $form->remove('gallery');
        $form->remove('img');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->getData();
            $photo->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManagerInterface->persist($photo);
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Photo modifiée avec succès !');
            return $this->redirectToRoute('app_admin_gallery_show', ['id' => $photo->getGallery()->getId()]);
        }


        return $this->render('photo/edit.html.twig', [
            'controller_name' => 'PhotoController',
            'form' => $form->createView(),
            'photo' => $photo,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request): Response
    {
        $photo = $this->photoRepository->find($request->get('id'));
        $gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($photo->getGallery()->getName()));

        $fileSystem = new Filesystem();
        if ($fileSystem->exists($gallery_directory . '/' . $photo->getPath())) {
            $fileSystem->remove($gallery_directory . '/' . $photo->getPath());
        }

        $this->entityManagerInterface->remove($photo);
        $this->entityManagerInterface->flush();
        $this->addFlash('success', 'Photo supprimée avec succès !');
        return $this->redirectToRoute('app_admin_gallery_show', ['id' => $photo->getGallery()->getId()]);
    }
}
