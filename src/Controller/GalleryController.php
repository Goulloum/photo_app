<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Form\GalleryType;
use App\Form\PhotoType;
use App\Repository\GalleryRepository;
use App\Service\GalleryService;
use App\Util\FileUtil;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/gallery', name: 'app_gallery_')]
class GalleryController extends AbstractController
{

    public function __construct(private GalleryRepository $galleryRepository, private EntityManagerInterface $entityManager, private GalleryService $galleryService, private FileUtil $fileUtil)
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $galleries = $this->galleryRepository->findAll();

        return $this->render('gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'galleries' => $galleries
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show($id): Response
    {
        $gallery = $this->galleryRepository->find($id);

        return $this->render('gallery/show.html.twig', [
            'controller_name' => 'GalleryController',
            'gallery' => $gallery
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {

        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gallery = $form->getData();
            $gallery->setCreatedAt(new \DateTimeImmutable());
            $gallery->setUpdatedAt(new \DateTimeImmutable());
            $image = $form->get('background')->getData();
            if ($image) {
                try {
                    $fileName = $this->galleryService->saveBackgroundImgFile($gallery, new File($image));
                } catch (Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                    return $this->redirectToRoute('app_admin_gallery');
                }

                $gallery->setBackgroundPath($fileName);
            }
            $this->entityManager->persist($gallery);
            $this->entityManager->flush();
            $this->addFlash('success', 'La gallerie a bien été créée !');
            return $this->redirectToRoute('app_admin_gallery_show', ['id' => $gallery->getId()]);
        }

        return $this->render('gallery/create.html.twig', [
            'controller_name' => 'GalleryController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, SluggerInterface $slugger, int $id): Response
    {
        $fileSystem = new Filesystem();
        //Get current gallery and pass it to the form
        $gallery = $this->galleryRepository->find($id);
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
                $this->galleryService->renameGalleryDirectory($oldGalleryName, $gallery->getName());
            }

            $new_gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));

            //Upload new image if there is one
            if ($image) {
                //Delete old image if there is one
                if ($gallery->getBackgroundPath() != null) {
                    $this->fileUtil->removeFile($new_gallery_directory . '/' . $gallery->getBackgroundPath());
                }
                //Upload new image
                try {
                    $newFileName = $this->galleryService->saveBackgroundImgFile($gallery, new File($image));
                } catch (Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                    return $this->redirectToRoute('app_admin_gallery');
                }
                $gallery->setBackgroundPath($newFileName);
            } else {
                $gallery->setBackgroundPath($gallery->getBackgroundPath());
            }

            $this->entityManager->persist($gallery);
            $this->entityManager->flush();
            $this->addFlash('success', 'La gallerie a bien été modifiée !');
            return $this->redirectToRoute('app_admin_gallery');
        }

        return $this->render('gallery/edit.html.twig', [
            'controller_name' => 'GalleryController',
            'form' => $form->createView(),
            'gallery' => $gallery
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id): Response
    {
        $gallery = $this->galleryRepository->find($id);
        if (!$gallery) {
            $this->addFlash('danger', 'La gallerie n\'existe pas !');
            return $this->redirectToRoute('app_admin_gallery');
        }

        $gallery_directory = $this->getParameter('gallery_images_directory') . str_replace(' ', '_', strtolower($gallery->getName()));
        $this->fileUtil->removeFile($gallery_directory);
        foreach ($gallery->getPhotos() as $photo) {
            $this->entityManager->remove($photo);
        }
        $this->entityManager->remove($gallery);
        $this->entityManager->flush();
        $this->addFlash('success', 'La gallerie a bien été supprimée !');
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
                $this->addFlash('danger', 'Vous devez ajouter une image !');
                return $this->redirectToRoute('app_gallery_photo_create', ['id' => $gallery->getId()]);
            }
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

            if (!$fileSystem->exists($gallery_directory)) {
                $this->addFlash('danger', 'Une erreur est survenue, aucune gallerie n\'existe avec le nom ' . $gallery->getName() . ' !');
                return $this->redirectToRoute('app_gallery_photo_create', ['id' => $gallery->getId()]);
            }

            try {
                $image->move(
                    $gallery_directory,
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image !');
                return $this->redirectToRoute('app_admin_gallery');
            }

            $photo->setPath($newFilename);

            $photo->setGallery($gallery);
            $entityManager->persist($photo);
            $entityManager->flush();
            $this->addFlash('success', 'La photo à bien été ajoutée !');
            return $this->redirectToRoute('app_admin_gallery_show', ['id' => $gallery->getId()]);
        }




        return $this->render('gallery/create_photo.html.twig', [
            'controller_name' => 'GalleryController',
            'form' => $form->createView()
        ]);
    }
}
