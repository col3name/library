<?php

namespace App\Repository;

use App\Entity\BookCopy;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookCopy::class);
    }

    /**
     * @param $readerId
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findIssuanceHistory($readerId, $page = 1, $limit = 5)
    {
        $builder = $this->getQueryBuilder();
        $offset = $page * $limit - $limit + 1;

        return $builder->setParameter('readerId', $readerId)
            ->select('issuance')
            ->from('App:Issuance', 'issuance')
            ->leftJoin('issuance.reader', 'reader')
            ->leftJoin('issuance.bookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book')
            ->where('reader.id = :readerId')
            ->orderBy('issuance.issueDate')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $readerId
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findTakenBook($readerId, $page = 1, $limit = 5) {
        $builder = $this->getQueryBuilder();
        $offset = $page * $limit - $limit + 1;

        return $builder->setParameter('readerId', $readerId)
//            ->select('issuance')
            ->addSelect('issuance.id as issuanceId')
            ->addSelect('issuance.issueDate')
            ->addSelect('issuance.deadlineDate')
            ->addSelect('reader.id as readerId')
            ->addSelect('reader.username as readerName')
            ->addSelect('bookCopy.id as bookCopyId')
            ->addSelect('book.name as bookName')
            ->addSelect('book.description as bookDescription')
            ->addSelect('bookCopy.imagePath')
            ->from('App:Issuance', 'issuance')
            ->leftJoin('issuance.reader', 'reader')
            ->leftJoin('issuance.bookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book')
            ->where('reader.id = :readerId AND issuance.releaseDate IS NULL')
            ->getQuery()
            ->execute();

    }
    /**
     * @param int $userId
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findUserComments(int $userId, $page = 1, int $limit = Comment::COMMENT_LIMIT) {
        $qb = $this->getQueryBuilder();

        $qb->setParameter('1', $userId)
            ->select('user')
            ->from('App:User', 'user')
            ->leftJoin('user.commentsAuthored', 'comment')
            ->where($qb->expr()->eq('user.id', '?1'))
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit)
//            ->getQuery()
//            ->execute()
            ;

        return $this->createPaginator($qb, $page);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param int $page
     * @param int $limit
     * @param string $orderBy
     * @return Pagerfanta
     */
    private function createPaginator(QueryBuilder $queryBuilder,
                                     int $page,
                                     int $limit = 10): Pagerfanta
    {
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        return $paginator;
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