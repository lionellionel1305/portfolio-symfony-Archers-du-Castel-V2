<?php
// src/Repository/AlbumRepository.php
namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    // Tu peux ajouter ici des méthodes personnalisées si besoin
}
