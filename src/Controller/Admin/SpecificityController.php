<?php

namespace App\Controller\Admin;

use Twig\Environment;
use App\Entity\Specificity;
use App\Form\SpecificityType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SpecificityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

#[IsGranted('ROLE_ADMIN')]

#[Route('/admin/specificites', name: 'admin.specificities.', host: 'localhost')]

class SpecificityController extends AbstractController
{

    public readonly Request $request;

    function __construct(
        private readonly Environment $twig,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
        private readonly FormFactoryInterface $formFactory,
        private readonly SpecificityRepository $specificityRepository
    )
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }


    #[Route('/', name: 'index', methods: ['GET'])]
    function index(): Response
    {
        /* if (!$this->security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('properties.index');
        } */

        $specificities = $this->specificityRepository->paginateSpecificities(
            $this->request->get('page', 1), 20
        );
        return $this->render('admin/specificity/index.html.twig', [
            'specificities' => $specificities
        ]);
    }


    #[Route(
        '/{slug}-{specificity}',
        name: 'show',
        methods: ['GET'],
        requirements: ['slug' => '[a-zA-Z0-9-]+', 'specificity' => Requirement::DIGITS]
    )]
    function show(Specificity $specificity, string $slug): Response
    {
        $specificitySlug = $specificity->getSlug();
        if ($slug !== $specificitySlug) {
            return $this->redirectToRoute(
                'admin.specificities.show', ['slug' => $specificitySlug, 'specificity' => $specificity->getId()]
            );
        }

        return $this->render('admin/specificity/show.html.twig', [
            'specificity' => $this->specificityRepository->find($specificity)
        ]);
    }


    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    function create(SluggerInterface $slugger): Response
    {
        $specificity = new Specificity($slugger);
        $form = $this->formFactory->create(SpecificityType::class, $specificity);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($specificity);
            $this->em->flush();
            $this->addFlash('success', 'La spécificité a bien été créee');
            return $this->redirectToRoute('admin.specificities.index');
        }

        return $this->render('admin/specificity/form.html.twig', [
            'form' => $form,
            'specificity' => $specificity
        ]);
    }


    #[Route(
        '/{specificity}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['specificity' => Requirement::DIGITS]
    )]
    function edit(Specificity $specificity): Response
    {
        $specificity = $specificity;
        $form = $this->formFactory->create(SpecificityType::class, $specificity);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'La spécificité a bien été éditée');
            return $this->redirectToRoute('admin.specificities.index');
        }

        return $this->render('admin/specificity/form.html.twig', [
            'form' => $form,
            'specificity' => $specificity
        ]);
    }


    #[Route(
        '/{specificity}',
        name: 'delete',
        methods: ['DELETE'],
        requirements: ['specificity' => Requirement::DIGITS]
    )]
    function delete(Specificity $specificity): Response
    {
        $this->em->remove($specificity);
        $this->em->flush();
        $this->addFlash('success', 'La spécificité a bien été supprimée');
        return $this->redirectToRoute('admin.specificities.index');
    }

}
