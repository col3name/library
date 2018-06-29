<?php

namespace App\Repository;

use App\Entity\BookCopy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use function Sodium\library_version_minor;

/**
 * Class BookCopyRepository
 * @package App\Repository
 */
class BookCopyRepository extends ServiceEntityRepository
{
    /**
     * BookCopyRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookCopy::class);
    }

    /**
     * @param string $query
     * @return string
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * @param $bookCopyId
     * @return array|mixed
     */
    public function countLike($bookCopyId)
    {
        $builder =  $this->getQueryBuilder();

        return $builder
            ->select('COUNT(user) AS countLike')
            ->from('App:BookCopy', 'a')
            ->leftJoin('a.userFavoritesBook', 'user')
            ->where($builder->expr()->eq('a.id', ':bookCopyId'))
            ->orderBy('countLike', 'DESC')
            ->setParameter('bookCopyId', $bookCopyId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $search
     * @param int $limit
     * @return mixed
     */
    public function findBySearch($search, $limit = BookCopy::MAX_COUNT)
    {
        $builder = $this->getQueryBuilder();
        $trimmedQuery = $this->sanitizeSearchQuery($search);

        return $builder
            ->setParameter('2', '%' . $trimmedQuery . '%')
            ->select('bookCopy.id as bookCopyId')
            ->addSelect('bookCopy.imagePath')
            ->addSelect('book.name')
            ->addSelect('book.description')
            ->from('App:BookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book')
            ->where($builder->expr()->like('book.name', '?2'))
            ->getQuery()
            ->execute();
    }

    /**
     * @param $genreId
     * @param int $limit
     * @return mixed
     */
    public function findSimilarBooks($genreId, $limit = 5)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->setParameter('id', $genreId)
            ->select('bookCopy')
            ->from('App:BookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book')
            ->leftJoin('book.genresBook', 'genres')
            ->where($qb->expr()->eq('genres.id', ':id'))
            ->orderBy('book.name')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array|null $options
     * @return mixed
     */
    public function findForCatalog(int $page, int $limit = BookCopy::NUM_ITEMS, array $options = null)
    {
        $builder = $this->getQueryBuilder();

        $builder
            ->select('bookCopy')
            ->from('App:BookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book');

        $genreId = $options['genreId'];
        if (isset($genreId) && is_numeric($genreId)) {
            $builder->innerJoin('book.genresBook', 'genresBook', 'WITH', 'genresBook.id = :genreId')
                ->setParameter('genreId', $genreId);
        }

        $authorId = $options['authorId'];
        if (isset($genreId) && is_numeric($authorId)) {
            $builder->innerJoin('book.authorsBook', 'authorsBook', 'WITH', 'authorsBook.id = :authorId')
                ->setParameter('authorId', $authorId);
        }

        $this->sortCatalogData($options, $builder);

        $offset = ($page - 1) * $limit;
        return $builder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $bookId
     * @return mixed
     */
    public function findGenreId(int $bookId)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->setParameter('bookId', $bookId)
            ->select('genre.id as genreId')
            ->from('App:Genre', 'genre')
            ->innerJoin('genre.books', 'books', 'WITH', 'books.id = :bookId')
            ->getQuery()
            ->execute();
    }

    /**
     * @return mixed
     */
    public function countBookCopy()
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->select('count(bookCopy.id)')
            ->from('App:BookCopy', 'bookCopy')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function findLatest($limit = BookCopy::MAX_COUNT)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder->select('p')
            ->from('App:BookCopy', 'p')
            ->leftJoin('p.book', 'b')
            ->orderBy('b.publicationYear', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $bookCopyId
     * @return mixed
     */
    public function averageRating(int $bookCopyId)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->setParameter('bookCopyId', $bookCopyId)
            ->select('AVG(rating.rating) as average')
            ->from('App:Rating', 'rating')
            ->leftJoin('rating.bookCopy', 'bookCopy')
            ->where('bookCopy.id = :bookCopyId')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $bookCopyId
     * @return mixed
     */
    public function findAuthorsOfBookRating(int $bookCopyId)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->setParameter('bookCopyId', $bookCopyId)
            ->select('author.id as authorId')
            ->from('App:Rating', 'rating')
            ->leftJoin('rating.bookCopy', 'bookCopy')
            ->leftJoin('rating.author', 'author')
            ->where($queryBuilder->expr()->eq('bookCopy.id', ':bookCopyId'))
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $bookCopyId
     * @return mixed
     */
    public function findAuthorsOfIssuance(int $bookCopyId)
    {
        $queryBuilder = $this->getQueryBuilder();

        $now = new \DateTime();

        return $queryBuilder
            ->select('reader.id as authorId')
            ->from('App:Issuance', 'issuance')
            ->leftJoin('issuance.bookCopy', 'bookCopy')
            ->leftJoin('issuance.reader', 'reader')
            ->where($queryBuilder->expr()->eq('bookCopy.id', ':bookCopyId'))
            ->setParameter('bookCopyId', $bookCopyId)
            ->andWhere('issuance.issueDate <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function findPopular($limit = BookCopy::MAX_COUNT)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->select('AVG(rating.rating) as avarage')
            ->addSelect('COUNT(rating.id) as number')
            ->addSelect('rating as rate')
            ->from('App:Rating', 'rating')
            ->groupBy('rating.bookCopy')
            ->orderBy('AVG(rating.rating)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $bookCopyId
     * @return QueryBuilder
     */
    public function countFreeBooks($bookCopyId)
    {
        $builder = $this->getQueryBuilder();

        $builder
            ->select('(bookCopy.count - count(issuance.id)) as countFreeBooks')
            ->addSelect('issuance.id as issuanceId')
            ->addSelect('reader.id as readerId')
            ->from('App:Issuance', 'issuance')
            ->leftJoin('issuance.reader', 'reader')
            ->leftJoin('issuance.bookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book')
            ->where('bookCopy.id = :bookCopyId AND issuance.releaseDate IS NULL')
            ->setParameter('bookCopyId', $bookCopyId);

//        $this->bookedBookCopy($builder, $bookCopyId);

        return $builder
            ->getQuery()
            ->execute();
    }

    /**
     * @param $bookCopyId
     * @return mixed
     */
    public function whoHasBookCopy($bookCopyId)
    {
        $builder = $this->getQueryBuilder();

        $builder
            ->addSelect('issuance.id as issuanceId')
            ->addSelect('issuance.deadlineDate as deadlineDate')
            ->addSelect('reader.id as readerId')
            ->addSelect('reader.username as readerName')
            ->addSelect('reader.avatar as readerAvatar')
            ->addSelect('bookCopy.id as bookCopyId')
            ->addSelect('bookCopy.imagePath as bookCopyImagePath')
            ->from('App:Issuance', 'issuance')
            ->leftJoin('issuance.reader', 'reader')
            ->leftJoin('issuance.bookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book')
            ->where('bookCopy.id = :bookCopyId AND issuance.releaseDate IS NULL')
            ->setParameter('bookCopyId', $bookCopyId);

        return $builder
            ->groupBy('issuance.id')
            ->getQuery()
            ->execute();
    }

    /**
     * @param $getId
     * @param $getId1
     */
    public function userRateBook($getId, $getId1)
    {
    }

    /**
     * @param QueryBuilder $builder
     * @param $bookCopyId
     * @return QueryBuilder
     */
    private function bookedBookCopy(QueryBuilder $builder, $bookCopyId)
    {
        $builder
            ->leftJoin('issuance.reader', 'reader')
            ->leftJoin('issuance.bookCopy', 'bookCopy')
            ->leftJoin('bookCopy.book', 'book')
            ->where('bookCopy.id = :bookCopyId AND issuance.releaseDate IS NULL')
            ->setParameter('bookCopyId', $bookCopyId);
        return $builder;
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
     * @param array $options
     * @param $builder
     */
    private function sortCatalogData(array $options, QueryBuilder $builder): void
    {
        $orderBy = $options['orderBy'];
        if (isset($orderBy)) {
            $sortField = $options['sortField'];

            if ($sortField === 'bookName') {
                $builder->orderBy('book.name', $orderBy);
            } else if ($sortField === 'publicationYear') {
                $builder->orderBy('book.publicationYear', $orderBy);
            }
        }
    }
}