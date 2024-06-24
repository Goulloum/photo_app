<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CreateCommentType;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comment', name: 'app_comment_')]
class CommentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManagerInterface, private PhotoRepository $photoRepository) {
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/post', name: 'post')]
    public function index(Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CreateCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUpdatedAt(new \DateTimeImmutable());

            
            $photo = $this->photoRepository->find($form->get('photoId')->getData());
            if(!$photo) {
                $this->addFlash('danger', 'La photo n\'existe pas !');
                return $this->redirectToRoute('app_gallery_index');
            }
            $comment->setPhoto($photo);

            $this->entityManagerInterface->persist($comment);
            $this->entityManagerInterface->flush();

            $this->addFlash('success', 'Le commentaire a été ajouté avec succès !');

            return $this->redirectToRoute('app_gallery_index');
        }
        
    }
}
