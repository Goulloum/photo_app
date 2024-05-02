<?php

namespace App\Controller;

use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use App\Service\PhotoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PhotoController extends AbstractController
{
    private PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    #[Route('/photo/edit/{id}', name: 'app_photo_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, SluggerInterface $slugger): Response
    {
        $photo = $photoRepository->find($request->get('id'));
        $form = $this->createForm(PhotoType::class, $photo);
        $form->remove('gallery');
        $form->remove('img');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->getData();
            $photo->setUpdatedAt(new \DateTimeImmutable());
            $entityManagerInterface->persist($photo);
            $entityManagerInterface->flush();
            $this->addFlash('success', 'Photo modifiée avec succès !');
            return $this->redirectToRoute('app_admin_gallery_show', ['id' => $photo->getGallery()->getId()]);
        }


        return $this->render('photo/edit.html.twig', [
            'controller_name' => 'PhotoController',
            'form' => $form->createView(),
            'photo' => $photo,
        ]);
    }

    #[Route('/photo/{id}', name: 'app_photo_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->photoService->delete($id);
            $this->addFlash('success', 'Photo supprimée avec succès !');
            return new Response('Photo supprimée avec succès !', Response::HTTP_OK);
        } catch (\Exception $e) {
            switch (get_class($e)) {
                case 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':
                    return new Response('Photo non trouvée !', Response::HTTP_NOT_FOUND);
                default:
                    return new Response('Une erreur est survenue !', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
