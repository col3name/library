<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class TaskRepository
 * @package App\Repository
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param $search
     * @return mixed
     */
    public function searchTag($search)
    {
        $builder = $this->getQueryBuilder();
        $trimmedQuery = $this->sanitizeSearchQuery($search);

        return $builder
            ->setParameter('2', '%' . $trimmedQuery . '%')
            ->select('tag.name')
            ->from('App:Tag', 'tag')
            ->where($builder->expr()->like('tag.name', '?2'))
            ->getQuery()
            ->execute();
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
     * @param string $query
     * @return string
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }
}