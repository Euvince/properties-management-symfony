<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home', methods: ['GET'])]
    function index(Request $request, PropertyRepository $propertyRepository): Response
    {
        $properties = $propertyRepository->findLatestVisibleProperties(
            $request->get('page', 1), 20
        );
        return $this->render('home/index.html.twig', [
            'properties' => $properties
        ]);
    }

}
