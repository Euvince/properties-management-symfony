<?php

namespace App\Controller\Admin;

use Twig\Environment;
use App\Entity\Heating;
use App\Form\HeatingType;
use App\Repository\HeatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]

#[Route('/admin/heaters', name: 'admin.heaters.', host: 'localhost')]

class HeatingController extends AbstractController
{

    public readonly Request $request;

    function __construct(
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
        private readonly FormFactoryInterface $formFactory,
        private readonly HeatingRepository $heatingRepository
    )
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }


    #[Route('/', name: 'index', methods: ['GET'])]
    function index(): Response
    {
        $heaters = $this->heatingRepository->paginateHeaters(
            $this->request->get('page', 1), 20
        );
        return $this->render('admin/heating/index.html.twig', [
            'heaters' => $heaters
        ]);
    }


    #[Route(
        '/{slug}-{heating}',
        name: 'show',
        methods: ['GET'],
        requirements: ['slug' => '[a-zA-Z0-9-]+', 'heating' => Requirement::DIGITS]
    )]
    function show(Heating $heating, string $slug): Response
    {
        $heatingSlug = $heating->getSlug();
        if ($slug !== $heatingSlug) {
            return $this->redirectToRoute(
                'admin.heaters.show', ['slug' => $heatingSlug, 'heating' => $heating->getId()]
            );
        }

        return $this->render('admin/heating/show.html.twig', [
            'heating' => $this->heatingRepository->find($heating)
        ]);
    }


    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    function create(SluggerInterface $slugger, ValidatorInterface $validator): Response
    {
        $heating = new Heating($slugger);
        $form = $this->formFactory->create(HeatingType::class, $heating);
        $form->handleRequest($this->request);
        /* if ($form->isSubmitted()) dd($validator->validate($heating)); */
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($heating);
            $this->em->flush();
            $this->addFlash('success', 'Le chauffage a bien été crée');
            return $this->redirectToRoute('admin.heaters.index');
        }

        return $this->render('admin/heating/form.html.twig', [
            /* 'form' => $form->createView(), */
            'form' => $form,
            'heating' => $heating
        ]);
    }


    #[Route(
        '/{heating}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['heating' => Requirement::DIGITS]
    )]
    function edit(Heating $heating): Response
    {
        $heating = $heating;
        $form = $this->formFactory->create(HeatingType::class, $heating);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'La chauffage a bien été édité');
            return $this->redirectToRoute('admin.heaters.index');
        }

        return $this->render('admin/heating/form.html.twig', [
            'form' => $form,
            'heating' => $heating
        ]);
    }


    #[Route(
        '/{heating}',
        name: 'delete',
        methods: ['DELETE'],
        requirements: ['heating' => Requirement::DIGITS]
    )]
    function delete(Heating $heating): Response
    {
        $this->em->remove($heating);
        $this->em->flush();
        $this->addFlash('success', 'La chauffage a bien été supprimé');
        return $this->redirectToRoute('admin.heaters.index');
    }

}
