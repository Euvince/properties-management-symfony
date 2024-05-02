<?php

namespace App\EventSubscriber;

use \App\Events\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    function __construct(
        private readonly Security $security,
        private readonly MailerInterface $mailer,
        private readonly RequestStack $requestStack,
    )
    {
    }

    function onContactRequestEvent(ContactRequestEvent $event): void
    {
        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        $data = $event->getData();
        $mail = (new TemplatedEmail())
            ->context(['data' => $data])
        ;
        $request = $this->requestStack->getCurrentRequest();
        if ($request->attributes->get('_route') === 'properties.show') {
            $mail->to($data->getProperty()->getUser()->getEmail())
                // ->to('Support @gmail.com') Dans le cas ou c'est l'agence on contact et non le propriétaire
                ->from($user->getEmail())
                // ->from($data->getEmail()) Dans le cas ou on ne voudrait acheter le bien pour soi
                ->subject('Nouvelle demande de contact sur un bien immobilier')
                ->htmlTemplate('emails/property-contact.html.twig')
            ;
        }
        elseif ($request->attributes->get('_route') === 'contact') {
            $mail
                ->to('Support@gmail.com')
                ->from($data->getEmail())
                ->subject('Nouvelle demande de contact vers l\'agence')
                ->htmlTemplate('emails/contact.html.twig')
            ;
        }

        $this->mailer->send($mail);
    }

    function onLogin(InteractiveLoginEvent $event): void
    {
        /**
         * @var User $user
         */
        $user = $event->getAuthenticationToken()->getUser();
           $mail = (new TemplatedEmail())
            ->to($user->getEmail())
            ->from('support@gmail.com')
            ->subject('Nouvelle connexion détectée')
            ->text('Nouvelle connexion détectée et réussi avec succès')
        ;
        $this->mailer->send($mail);
    }

    function onLogout(LogoutEvent $event): void
    {
        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        $mail = (new TemplatedEmail())
            ->to($user->getEmail())
            ->from('support@gmail.com')
            ->subject('Déconnexion détectée')
            ->text('Déconnexion éffectuée avec succès')
        ;
        $this->mailer->send($mail);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            /* LogoutEvent::class => 'onLogout',
            InteractiveLoginEvent::class => 'onLogin', */
            ContactRequestEvent::class => 'onContactRequestEvent',
        ];
    }
}
