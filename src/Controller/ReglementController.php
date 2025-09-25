<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReglementController extends AbstractController
{
    #[Route('/réglement-intérieur', name: 'app_reglement')]
    public function index(): Response
    {
        return $this->render('reglement/index.html.twig', [
            'controller_name' => 'ReglementController',
        ]);
    }
}
