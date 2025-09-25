<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PartenairesController extends AbstractController
{
    #[Route('/partenaires-du-club', name: 'app_partenaires')]
    public function index(): Response
    {
        return $this->render('partenaires/index.html.twig', [
            'controller_name' => 'PartenairesController',
        ]);
    }
}
