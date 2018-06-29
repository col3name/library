<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function findUserRating($userId, $bookCopyId)
    {
        $builder = $this->getQueryBuilder();

        return $builder
            ->setParameter('userId', $userId)
            ->select('rating')
            ->from('App:Rating', 'rating')
            ->leftJoin('rating.bookCopy', 'bookCopy')
            ->leftJoin('rating.author', 'author')
            ->where($builder->expr()->eq('author.id', '?userId'))
            ->getQuery()
            ->execute()
            ;
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder();
    }

}