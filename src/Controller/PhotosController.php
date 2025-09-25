<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/Les-photos/du-Club')]
final class PhotosController extends AbstractController
{
    #[Route('', name: 'app_photos', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository, AlbumRepository $albumRepository): Response
    {
        // Toutes les photos individuelles (si tu veux)
        $photos = $photoRepository->findAll();

        // Tous les albums pour afficher 1 photo par album
        $albums = $albumRepository->findAll();

        return $this->render('photos/gallery.html.twig', [
            'photos' => $photos, // facultatif si tu veux juste albums
            'albums' => $albums,
        ]);
    }



    // Ajouter une photo (admin seulement)
    #[Route('/add', name: 'photo_add', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $uploadedFile = $request->files->get('file');

            if (!$uploadedFile) {
                $this->addFlash('error', 'Veuillez sélectionner un fichier.');
                return $this->redirectToRoute('photo_add');
            }

            $photo = new Photo();
            $photo->setName($request->request->get('name') ?? 'Photo sans nom');

            // Génération du nom unique et déplacement du fichier
            $filename = uniqid('photo_').'.'.$uploadedFile->guessExtension();
            $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/photos';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            try {
                $uploadedFile->move($uploadDir, $filename);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'enregistrer le fichier.');
                return $this->redirectToRoute('photo_add');
            }

            $photo->setFilename($filename);
            $photo->setCreatedAt(new \DateTimeImmutable());

            $em->persist($photo);
            $em->flush();

            $this->addFlash('success', 'Photo ajoutée avec succès !');
            return $this->redirectToRoute('app_photos');
        }

        return $this->render('photos/add.html.twig');
    }

    // Modifier une photo (admin seulement)
    #[Route('/{id}/edit', name: 'photo_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Photo $photo, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $photo->setName($request->request->get('name') ?? $photo->getName());

            $uploadedFile = $request->files->get('file');
            if ($uploadedFile) {
                $filename = uniqid('photo_').'.'.$uploadedFile->guessExtension();
                $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/photos';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                try {
                    $uploadedFile->move($uploadDir, $filename);
                    $photo->setFilename($filename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'enregistrer le fichier.');
                    return $this->redirectToRoute('photo_edit', ['id' => $photo->getId()]);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Photo mise à jour avec succès !');
            return $this->redirectToRoute('app_photos');
        }

        return $this->render('photos/edit.html.twig', [
            'photo' => $photo,
            'albums' => $albums,
        ]);
    }

    // Supprimer une photo (admin seulement)
    #[Route('/{id}/delete', name: 'photo_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Photo $photo, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {
            $em->remove($photo);
            $em->flush();
            $this->addFlash('success', 'Photo supprimée avec succès !');
        }

        return $this->redirectToRoute('app_photos');
    }
}
