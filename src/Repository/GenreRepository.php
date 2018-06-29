<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder();
    }

    public function findAllBooks()
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->select('genre')
            ->from('App:Genre', 'genre')
            ->innerJoin('genre.books', 'books', 'WITH', 'books.id = 8')
            ->getQuery()
            ->execute()
            ;
    }
}