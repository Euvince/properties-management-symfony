<?php

namespace App\Controller\Admin;

use Faker\Factory;
use Twig\Environment;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\PropertyType;
use App\Repository\HeatingRepository;
use App\Security\Voter\PropertyVoter;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SpecificityRepository;
use App\Repository\PropertyTypeRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[IsGranted('ROLE_USER')]

#[Route('/admin/properties', name: 'admin.properties.', host: 'localhost')]

class PropertyController extends AbstractController
{

    public readonly Request $request;

    function __construct(
        private readonly Environment $twig,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
        private readonly CacheManager $cacheManager,
        private readonly UploaderHelper $uploaderHelper,
        private readonly FormFactoryInterface $formFactory,
        private readonly PropertyRepository $propertyRepository
    )
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }


    #[IsGranted(PropertyVoter::LIST)]
    #[Route('/', name: 'index', methods: ['GET'])]
    function index(): Response
    {
        $search = new PropertySearch();

        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        $userId = $user->getId();
        $canListAll = $this->security->isGranted(PropertyVoter::LIST_ALL);
        $properties = $this->propertyRepository->paginateVisibleProperties(
            $this->request->get('page', 1), 20, $canListAll ? null : $userId, $search
        );
        return $this->render('admin/property/index.html.twig', [
            'properties' => $properties
        ]);
    }


    #[IsGranted(PropertyVoter::VIEW, subject: 'property')]
    #[Route(
        '/{slug}-{property}',
        name: 'show',
        methods: ['GET'],
        requirements: ['slug' => '[a-zA-Z0-9-]+', 'property' => Requirement::DIGITS]
    )]
    function show(Property $property, string $slug): Response
    {
        $propertySlug = $property->getSlug();
        if ($slug !== $propertySlug) {
            return $this->redirectToRoute(
                'admin.properties.show', ['slug' => $propertySlug, 'property' => $property->getId()]
            );
        }

        return $this->render('admin/property/show.html.twig', [
            'property' => $this->propertyRepository->find($property)
        ]);
    }


    #[IsGranted(PropertyVoter::CREATE)]
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    function create(
        ValidatorInterface $validator,
        HeatingRepository $heatingRepository,
        SpecificityRepository $specificityRepository,
        PropertyTypeRepository $propertyTypeRepository
    ): Response
    {
        $property = (new Property())
            ->setPropertyType($propertyTypeRepository->findOneBy(['name' => 'Appartement']))
            ->setTitle("Nouveau Bien de test")
            ->setSurface(120)
            ->setPrice(12000)
            ->setDescription("Nouveau bien dans la rue de la fontaine, à Montpellier.")
            ->setRooms(4)
            ->setBedrooms(8)
            ->setFloor(4)
            ->setAdress("Rue de la fontaine")
            ->setCity("Montpellier")
            ->setPostalCode(12455666)
        ;

        $faker = Factory::create('fr_FR');

        $heaters = $heatingRepository->findAll();
            foreach ($faker->randomElements($heaters, $faker->numberBetween(1, 2)) as $heater) {
                $property->addHeater($heater);
            }
        $specificities = $specificityRepository->findAll();
        foreach ($faker->randomElements($specificities, $faker->numberBetween(1, 2)) as $specificity) {
            $property->addSpecificity($specificity);
        }

        $form = $this->formFactory->create(PropertyType::class, $property);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $property->setUser($this->getUser());
            $this->em->persist($property);
            $this->em->flush();

            /**
             * var File $file
             */
            /* $file = $form->get('pictureFile')->getData();
            dd($file);
            dd($file->getClientOriginalName(), $file->getClientOriginalExtension());
            $filename = $property->getId() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/images/properties', $filename);
            $property->setPicture($filename); */
            /* $property->fileUpload($form); */

            $this->addFlash('success', 'Le Bien a bien été crée');
            return $this->redirectToRoute('admin.properties.index');
        }

        return $this->render('admin/property/form.html.twig', [
            /* 'form' => $form->createView(), */
            'form' => $form,
            'property' => $property
        ]);
    }


    #[IsGranted(PropertyVoter::EDIT, subject: 'property')]
    #[Route(
        '/{property}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['property' => Requirement::DIGITS]
    )]
    function edit(Property $property): Response
    {
        $property = $property;
        $form = $this->formFactory->create(PropertyType::class, $property);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * var File $file
             */
            /* $file = $form->get('pictureFile')->getData();
            dd($file);
            dd($file->getClientOriginalName(), $file->getClientOriginalExtension());
            $filename = $property->getId() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/images/properties', $filename);
            $property->setPicture($filename); */
            /* $property->fileUpload($form); */

            /* if ($property->getPictureFile() instanceof UploadedFile) {
                $this->cacheManager->remove($this->uploaderHelper->asset($property, 'pictureFile'));
            } */

            $this->em->flush();
            $this->addFlash('success', 'La Bien a bien été édité');
            return $this->redirectToRoute('admin.properties.index');
        }

        return $this->render('admin/property/form.html.twig', [
            'form' => $form,
            'property' => $property
        ]);
    }


    #[Route(
        '/{property}',
        name: 'delete',
        methods: ['DELETE'],
        requirements: ['type' => Requirement::DIGITS]
    )]
    function delete(Property $property): Response
    {
        $this->em->remove($property);
        $this->em->flush();

        /* $this->cacheManager->remove($this->uploaderHelper->asset($property, 'pictureFile')); */

        $this->addFlash('success', 'La Bien a bien été supprimé');
        return $this->redirectToRoute('admin.properties.index');
    }

}
