<?php

namespace App\Controller\User;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use App\Events\ContactRequestEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route(
        '/contact',
        name: 'contact',
        methods: ['GET', 'POST'],
        host: 'localhost'
    )]
    function contact(
        Request $request,
        Security $security,
        EventDispatcherInterface $dispatcher
    ) : Response
    {
        /**
         * var App\Entity\User $user
         */
        $user = $security->getUser();

        $data = $user === NULL
            ?   (new ContactDTO())
                ->setFirstname('DOE')
                ->setLastname('Jonh')
                ->setEmail('jonh@doe.fr')
                ->setPhone('+33 445 45 44 12 3')

            :   (new ContactDTO())
                ->setFirstname($user->getUsername())
                ->setEmail($user->getEmail())
                ->setMessage('Super appartement')
        ;

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $dispatcher->dispatch(new ContactRequestEvent($data));
                $this->addFlash('success', 'Votre message a bien été envoyé');
                return $this->redirectToRoute('contact');
            }catch (\Exception $e) {
                throw new \Exception("Une erreur s'est produite." . $e->getMessage());
            }
        }

        return $this->render('user/contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}