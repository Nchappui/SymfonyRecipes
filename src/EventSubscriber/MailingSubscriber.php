<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer) {}

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $contact = $event->data;
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

        $this->mailer->send($email);
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User) {
            return;
        }

        $email = (new Email())
            ->from($user->getEmail())
            ->to('support@demo.fr')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("Connexion")
            ->text('Vous vous êtes connecté');

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }
}
