<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'app_user_')]
class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordHasherInterface, private UserRepository $userRepository) {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('app_admin_user');
        }

        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView()
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if(!$user) {
            $this->addFlash('danger', 'Utilisateur non trouvé !');
            return $this->redirectToRoute('app_admin_user');
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        return $this->redirectToRoute('app_admin_user');
    }
}
