<?php

namespace App\Controller\Admin;

use App\Form\PTType;
use Twig\Environment;
use App\Entity\PropertyType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PropertyTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]

#[Route('/admin/types', name: 'admin.types.', host: 'localhost')]

class PropertyTypeController extends AbstractController
{

    public readonly Request $request;

    function __construct(
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
        private readonly FormFactoryInterface $formFactory,
        private readonly PropertyTypeRepository $propertyTypeRepository
    )
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }


    #[Route('/', name: 'index', methods: ['GET'])]
    function index(): Response
    {
        $types = $this->propertyTypeRepository->paginateTypes(
            $this->request->get('page', 1), 20
        );
        return $this->render('admin/propertyType/index.html.twig', [
            'types' => $types
        ]);
    }


    #[Route(
        '/{slug}-{type}',
        name: 'show',
        methods: ['GET'],
        requirements: ['slug' => '[a-zA-Z0-9-]+', 'type' => Requirement::DIGITS]
    )]
    function show(PropertyType $type, string $slug): Response
    {
        $typeSlug = $type->getSlug();
        if ($slug !== $typeSlug) {
            return $this->redirectToRoute(
                'admin.types.show', ['slug' => $typeSlug, 'type' => $type->getId()]
            );
        }

        return $this->render('admin/propertyType/show.html.twig', [
            'type' => $this->propertyTypeRepository->find($type)
        ]);
    }


    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    function create(SluggerInterface $slugger): Response
    {
        $type = new PropertyType($slugger);
        $form = $this->formFactory->create(PTType::class, $type);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($type);
            $this->em->flush();
            $this->addFlash('success', 'Le Type de bien a bien été crée');
            return $this->redirectToRoute('admin.types.index');
        }

        return $this->render('admin/propertyType/form.html.twig', [
            'form' => $form,
            'type' => $type
        ]);
    }


    #[Route(
        '/{type}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['heating' => Requirement::DIGITS]
    )]
    function edit(PropertyType $type): Response
    {
        $type = $type;
        $form = $this->formFactory->create(PTType::class, $type);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'La Type de bien a bien été édité');
            return $this->redirectToRoute('admin.types.index');
        }

        return $this->render('admin/propertyType/form.html.twig', [
            'form' => $form,
            'type' => $type
        ]);
    }


    #[Route(
        '/{type}',
        name: 'delete',
        methods: ['DELETE'],
        requirements: ['type' => Requirement::DIGITS]
    )]
    function delete(PropertyType $type): Response
    {
        $this->em->remove($type);
        $this->em->flush();
        $this->addFlash('success', 'La Type de bien a bien été supprimé');
        return $this->redirectToRoute('admin.types.index');
    }

}
