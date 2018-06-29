<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
//use Pagerfanta\Pagerfanta;
//use Pagerfanta\Adapter\DoctrineORMAdapter;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder();
    }

    public function findBySearch($search, $limit = 5)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->setParameter('2', '%' . $search . '%')
            ->select('book')
            ->from('App:Book', 'book')
            ->where($qb->expr()->like('book.name', '?2'))
            ->setMaxResults($limit)
            ->getQuery()
            ->execute()
            ;
    }

    public function findByGenre($genreId) {
        $builder = $this->getQueryBuilder();

        return $builder->select('book')
            ->from('App:Book', 'book')
            ->innerJoin('book.genresBook', 'genresBook', 'WITH', 'genresBook.id = :genreId')
            ->setParameter('genreId', $genreId)
            ->getQuery()
            ->execute()
            ;
    }

    public function getLikeInfo($bookCopyId) {
        $builder = $this->getQueryBuilder();

        return $builder->select('user.id as userId')
            ->addSelect('user.email')
            ->addSelect('bookCopy.id')
            ->from('App:BookCopy', 'bookCopy')
            ->innerJoin('bookCopy.userFavoritesBook', 'user', 'WITH', 'user.id = :bookCopyId')
            ->setParameter('bookCopyId', $bookCopyId)
            ->groupBy('userId')
            ->getQuery()
            ->execute()
            ;
    }
//
//    /**
//     * @param int $page
//     * @return Pagerfanta
//     */
//    public function findLatest($page = 1, $orderBy = 0)
//    {
//        $entityManager = $this->getEntityManager();
//        $queryBuilder = $entityManager->createQueryBuilder()
//            ->select('p')
//            ->from('App:Book', 'p')
//            ->orderBy('p.publicationYear', (($orderBy === 0) ? 'ASC' : 'DESC'));
//
//        return $this->createPaginator($queryBuilder, $page);
//    }
//
//    /**
//     * @param QueryBuilder $queryBuilder
//     * @param int $page
//     * @return Pagerfanta
//     */
//    private function createPaginator(QueryBuilder $queryBuilder, int $page): Pagerfanta
//    {
//        $adapter = new DoctrineORMAdapter($queryBuilder);
//        $paginator = new Pagerfanta($adapter);
//        $paginator->setMaxPerPage(Book::NUM_ITEMS);
//        $paginator->setCurrentPage($page);
//
//        return $paginator;
//    }
}