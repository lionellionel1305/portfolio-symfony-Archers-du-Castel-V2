<?php
// src/Controller/PopupController.php
namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PopupSettings;
use App\Form\PopupSettingsType;

class PopupController extends AbstractController
{
    #[Route('/popup/edit', name: 'popup_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, EntityManagerInterface $em)
    {
        $popup = $em->getRepository(PopupSettings::class)->find(1);
        if (!$popup) {
            throw $this->createNotFoundException('Popup non trouvé.');
        }

        $form = $this->createForm(PopupSettingsType::class, $popup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Popup mis à jour !');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('popup/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
