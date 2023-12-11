<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $email = (new TemplatedEmail())
                ->from($form->get('email')->getData())
                ->to('guillemin.mathieu@outlook.com')
                ->subject('Contact depuis le site')
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'nom' => $form->get('nom')->getData(),
                    'prenom' => $form->get('prenom')->getData(),
                    'mail' => $form->get('email')->getData(),
                    'message' => $form->get('message')->getData(),
                    'subject' => $form->get('subject')->getData(),
                ]);

            $mailer->send($email);
            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form->createView(),
        ]);
    }
}
