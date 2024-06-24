<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\CreateEventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/event', name: 'app_event_')]
class EventController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManagerInterface, private EventRepository $eventRepository) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $events = $this->eventRepository->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }




    #[IsGranted('ROLE_ADMIN')]
    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(CreateEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreator($this->getUser());
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManagerInterface->persist($event);
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('app_admin_event');
        }

        return $this->render('event/create.html.twig', [
            'controller_name' => 'EventController',
            'form' => $form->createView()
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $event = $this->eventRepository->find($id);
        if(!$event) {
            $this->addFlash('danger', 'Événement non trouvé !');
            return $this->redirectToRoute('app_admin_event');
        }
        $this->entityManagerInterface->remove($event);
        $this->entityManagerInterface->flush();
        $this->addFlash('success', 'Événement supprimé avec succès !');
        return $this->redirectToRoute('app_admin_event');
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/join/{id}', name: 'join', requirements: ['id' => '\d+'])]
    public function join(int $id): Response
    {
        $event = $this->eventRepository->find($id);
        if(!$event) {
            $this->addFlash('danger', 'Événement non trouvé !');
            return $this->redirectToRoute('app_event_index');
        }
        if($event->getParticipant()->contains($this->getUser())) {
            $event->removeParticipant($this->getUser());
        }else{
            $event->addParticipant($this->getUser());
        }
        $this->entityManagerInterface->flush();
        $this->addFlash('success', 'Vous avez rejoint l\'événement avec succès !');
        return $this->redirectToRoute('app_event_index');
    }
    
}
