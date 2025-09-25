<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/albums-photos/du-club')]
final class AlbumController extends AbstractController
{
    #[Route('', name: 'app_albums', methods: ['GET'])]
    public function index(AlbumRepository $albumRepository): Response
    {
        $albums = $albumRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('albums/gallery.html.twig', ['albums' => $albums]);
    }

    #[Route('/{id}', name: 'album_show', methods: ['GET'])]
    public function show(Album $album): Response
    {
        return $this->render('albums/show.html.twig', ['album' => $album]);
    }

    #[Route('/add', name: 'album_add', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
public function add(Request $request, EntityManagerInterface $em): Response
{
    if ($request->isMethod('POST')) {
        $album = new Album();
        $album->setName($request->request->get('name'));
        $album->setCreatedAt(new \DateTimeImmutable());

        $uploadBaseDir = $this->getParameter('kernel.project_dir').'/public/uploads/albums';
        if (!is_dir($uploadBaseDir)) mkdir($uploadBaseDir, 0777, true);

        $folderName = uniqid('album_');
        $uploadDir = $uploadBaseDir.'/'.$folderName;
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $uploadedFiles = $request->files->get('files', []);
        if (!is_array($uploadedFiles) || count($uploadedFiles) === 0) {
            $this->addFlash('error', 'Veuillez sélectionner au moins une photo.');
            return $this->redirectToRoute('album_add');
        }

        $fileNames = [];
        foreach ($uploadedFiles as $uploadedFile) {
            if (!$uploadedFile) continue;
            $extension = $uploadedFile->guessExtension() ?: 'jpg';
            $filename = uniqid().'.'.$extension;

            try {
                $uploadedFile->move($uploadDir, $filename);
                $fileNames[] = $folderName.'/'.$filename;
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'enregistrer : '.$uploadedFile->getClientOriginalName());
            }
        }

        if (count($fileNames) === 0) {
            $this->addFlash('error', 'Aucune photo valide n’a été ajoutée.');
            return $this->redirectToRoute('album_add');
        }

        $album->setFiles($fileNames);

        $em->persist($album);
        $em->flush();

        $this->addFlash('success', 'Album créé avec succès !');
        return $this->redirectToRoute('app_albums');
    }

    return $this->render('albums/add.html.twig');
}
 // Modifier un album
 #[Route('/{id}/edit', name: 'album_edit', methods: ['GET', 'POST'])]
 #[IsGranted('ROLE_ADMIN')]
 public function edit(Album $album, Request $request, EntityManagerInterface $em): Response
 {
     if ($request->isMethod('POST')) {
         $album->setName($request->request->get('name') ?? $album->getName());

         // Gestion des fichiers supplémentaires si nécessaire
         $uploadedFiles = $request->files->get('files', []);
         $uploadBaseDir = $this->getParameter('kernel.project_dir') . '/public/uploads/albums';
         if (!is_dir($uploadBaseDir)) mkdir($uploadBaseDir, 0777, true);

         $folderName = 'album_' . $album->getId();
         $uploadDir = $uploadBaseDir.'/'.$folderName;
         if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

         $fileNames = $album->getFiles(); // récupère les fichiers existants
         foreach ($uploadedFiles as $uploadedFile) {
             if (!$uploadedFile) continue;
             $extension = $uploadedFile->guessExtension() ?: 'jpg';
             $filename = uniqid().'.'.$extension;
             try {
                 $uploadedFile->move($uploadDir, $filename);
                 $fileNames[] = $folderName.'/'.$filename;
             } catch (\Exception $e) {
                 $this->addFlash('error', 'Impossible d\'enregistrer : '.$uploadedFile->getClientOriginalName());
             }
         }

         $album->setFiles($fileNames);

         $em->flush();
         $this->addFlash('success', 'Album mis à jour avec succès !');
         return $this->redirectToRoute('app_albums');
     }

     return $this->render('albums/edit.html.twig', ['album' => $album]);
 }

 // Supprimer un album
 #[Route('/{id}/delete', name: 'album_delete', methods: ['POST'])]
 #[IsGranted('ROLE_ADMIN')]
 public function delete(Album $album, EntityManagerInterface $em, Request $request): Response
 {
     if ($this->isCsrfTokenValid('delete'.$album->getId(), $request->request->get('_token'))) {
         $em->remove($album);
         $em->flush();
         $this->addFlash('success', 'Album supprimé avec succès !');
     }

     return $this->redirectToRoute('app_albums');
 }
}