<?php

namespace App\Repository;

use App\Entity\DesiredBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AuthorRepository
 * @package App\Repository
 */
class BookDesiredRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DesiredBook::class);
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