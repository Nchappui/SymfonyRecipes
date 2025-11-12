<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Event\ContactRequestEvent;
use App\Form\ContactDTOType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;

final class ContactController extends AbstractController
{
    #[Route("/contact", name: "contact")]
    function index(MailerInterface $mailer, Request $request, EventDispatcherInterface $dispatcher): Response
    {
        $contact = new ContactDTO(); // ← Plus explicite !

        $form = $this->createForm(ContactDTOType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $dispatcher->dispatch(new ContactRequestEvent($contact));
                $this->addFlash('success', "Le message a bien été envoyé");
            } catch (\Exception $e) {
                $this->addFlash('danger', "Echec de l'envoi de l'email");
            }
            // NOW IN A SUBSCRIBER IN App\EventSubscriber\MailingSubscriber
            /*
            try {
                $email = (new TemplatedEmail())
                    ->from($contact->getEmail())
                    ->to($contact->getEmailOfService())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject("Initial contact from {$contact->getFirstName()} {$contact->getLastName()}")
                    //->text($contact->getContent())
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['contact' => $contact]);

                $mailer->send($email);
            } catch (\Exception $e) {
                $this->addFlash('danger', "Echec de l'envoi de l'email");
            }
            */

            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form
        ]);
    }
}
