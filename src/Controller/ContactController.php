<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $nom = htmlspecialchars($request->request->get('nom'));
            $email = htmlspecialchars($request->request->get('email'));
            $message = htmlspecialchars($request->request->get('message'));
            $consent = $request->request->get('consent');

            if (!$consent) {
                $this->addFlash('error', "❌ Vous devez accepter l'utilisation de vos données.");
                return $this->redirectToRoute('app_contact');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', "❌ Email invalide.");
                return $this->redirectToRoute('app_contact');
            }

            // Création du lien mailto
            $to = 'lionel.lebreton@sfr.fr';
            $subject = rawurlencode("📩 Nouveau message de $nom (site web Archers du Castel)");
            $body = rawurlencode("Nom: $nom\nEmail: $email\n\nMessage:\n$message");
            $mailtoLink = "https://outlook.live.com/owa/?path=/mail/action/compose&to={$to}&subject={$subject}&body={$body}";

            // Redirection vers Hotmail avec le mail pré-rempli
            return $this->redirect($mailtoLink);
        }

        return $this->render('contact/index.html.twig');
    }
}
