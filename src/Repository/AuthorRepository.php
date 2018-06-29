<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AuthorRepository
 * @package App\Repository
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder();
    }

    /**
     * @param int $authorId
     * @return mixed
     */
    public function findById(int $authorId)
    {
        $builder = $this->getQueryBuilder();

        return $builder->select('author')
            ->from('App:Author', 'author')
            ->where($builder->expr()->eq('author.id', ':authorId'))
            ->setParameter('authorId', $authorId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return mixed
     */
    public function findAllBooks()
    {
        $builder = $this->getQueryBuilder();

        return $builder
            ->select('genre')
            ->from('App:Genre', 'genre')
            ->innerJoin('genre.books', 'books', 'WITH', 'books.id = 8')
            ->getQuery()
            ->execute()
            ;
    }
}