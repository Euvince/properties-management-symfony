<?php

namespace App\Controller\User;

use App\DTO\ContactDTO;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Events\ContactRequestEvent;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

#[Route('/properties', name: 'properties.', host: 'localhost')]

class PropertyController extends AbstractController
{

    public readonly Request $request;

    function __construct(
        private readonly Environment $twig,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly FormFactoryInterface $formFactory,
        private readonly PropertyRepository $propertyRepository
    )
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }


    #[Route('/', name: 'index', methods: ['GET'])]
    function index(): Response
    {
        $search = new PropertySearch();
        $form = $this->formFactory->create(PropertySearchType::class, $search);
        $form->handleRequest($this->request);

        $properties = $this->propertyRepository->paginateVisibleProperties(
            $this->request->get('page', 1), 20, null, $search
        );
        return $this->render('user/property/index.html.twig', [
            'form' => $form,
            'properties' => $properties
        ]);
    }


    #[IsGranted('ROLE_USER')]
    #[Route(
        '/{slug}-{property}',
        name: 'show',
        methods: ['GET', 'POST'],
        requirements: ['slug' => '[a-zA-Z0-9-]+', 'property' => Requirement::DIGITS]
    )]
    function show(
        string $slug,
        Property $property,
        MailerInterface $mailer,
        EventDispatcherInterface $dispatcher
    ): Response
    {
        $propertySlug = $property->getSlug();
        if ($slug !== $propertySlug) {
            return $this->redirectToRoute(
                'properties.show', ['slug' => $propertySlug, 'property' => $property->getId()]
            );
        }

        /**
         * @var App\Entity\User $user
         */
        $user = $this->security->getUser();

        $data = $user === NULL
            ?   (new ContactDTO())
                ->setProperty($property)
                ->setFirstname('DOE')
                ->setLastname('Jonh')
                ->setEmail('jonh@doe.fr')
                ->setPhone('+33 445 45 44 12 3')

            :   (new ContactDTO())
                ->setProperty($property)
                ->setFirstname($user->getUsername())
                ->setEmail($user->getEmail())
                ->setMessage('Super appartement')
        ;

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $dispatcher->dispatch(new ContactRequestEvent($data));
                $this->addFlash('success', 'Votre message a bien été envoyé');
                return $this->redirectToRoute('properties.show', ['slug' => $propertySlug, 'property' => $property->getId()]);
            }catch (\Exception $e) {
                throw new \Exception("Une erreur s'est produite." . $e->getMessage());
            }

            /* try {
                $mail = (new TemplatedEmail())
                    ->to($property->getUser()->getEmail())
                    ->to($data->getEmail())
                    ->from($user->getEmail())
                    ->subject('Nouvelle demande de contact sur un bien immobilier')
                    ->context(['data' => $data])
                    ->htmlTemplate('emails/property-contact.html.twig')
                ;
                $mailer->send($mail);
                $this->addFlash('success', 'Votre message a bien été envoyé');
                return $this->redirectToRoute('properties.show', ['slug' => $propertySlug, 'property' => $property->getId()]);
            }catch (\Exception $e) {
                throw new Exception("Une erreur s'est produite." . $e->getMessage());
            } */
        }

        return $this->render('user/property/show.html.twig', [
            'form' => $form,
            'property' => $this->propertyRepository->find($property)
        ]);
    }

}
