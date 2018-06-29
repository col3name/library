<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Issuance;
use App\Entity\Rating;
use App\Entity\BookCopy;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RatingType;
use App\Repository\AuthorRepository;
use App\Repository\BookCopyRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/catalog")
 */
class CatalogController extends Controller
{
    /**
     * @Route("/", name="catalog_page")
     * @param Request $request
     * @param BookCopyRepository $bookCopyRepository
     * @param AuthorRepository $authorRepository
     * @param GenreRepository $genreRepository
     * @return JsonResponse|Response
     */
    public function index(Request $request, BookCopyRepository $bookCopyRepository, GenreRepository $genreRepository, AuthorRepository $authorRepository)
    {
        $page = $request->get('page') ?? 1;
        $limit = $request->get('limit') ?? BookCopy::NUM_ITEMS;

        if ($request->isXmlHttpRequest()) {
            $options = $this->parseFilterOptions($request);
            $result = $this->getData($bookCopyRepository, $page, $limit, $options);
            return $this->json($result);
        }
        $options = $this->parseFilterOptions($request);

        $parameters = $this->getCatalogPageParameters($bookCopyRepository, $genreRepository, $authorRepository, $page, $limit, $options);
        return $this->render('catalog/index.html.twig', $parameters);
    }

    /**
     * @param Request $request
     * @param BookRepository $repository
     * @param GenreRepository $genreRepository
     * @return Response
     * @Route("/genre", name="genre_page")
     */
    public function genre(Request $request, BookRepository $repository, GenreRepository $genreRepository)
    {
        $parameters = $this->generateParameterGenrePage($request, $repository, $genreRepository);
        return $this->render('catalog/genre.html.twig', $parameters);
    }

    /**
     * @Route("/{id}", requirements={"\d+"}, name="catalog_show")
     * @param BookCopy $bookCopy
     * @param BookCopyRepository $bookCopyRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function show(BookCopy $bookCopy, BookCopyRepository $bookCopyRepository)
    {
        $userId = $this->getUserId();
        $parameters = $this->generateShowPageParameter($bookCopy, $bookCopyRepository, $userId);
        return $this->render('catalog/show.html.twig', $parameters);
    }

    /**
     * @param Request $request
     * @param BookCopyRepository $bookCopyRepository
     * @param BookCopy $bookCopy
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/{id}/take-book", requirements={"\d+"}, name="takeBook" )
     */
    public function takeBook(Request $request, BookCopyRepository $bookCopyRepository, BookCopy $bookCopy)
    {
        if ($request->isXmlHttpRequest()) {
            $existFreeBooks = $this->existFreeBooks($bookCopyRepository, $bookCopy);
            $user = $this->getUser();

            $userHasThisBook = $this->userHasThisBook($bookCopyRepository, $user->getId(), $bookCopy);

            if (!$existFreeBooks || $userHasThisBook) {
                $result['result'] = 'success';

                return $this->json($result);
            }

            $issuance = $this->setNewIssuance($bookCopy, $user);
            $this->saveEntity($issuance);
            $json['result']= 'good';

            return $this->json($json);
        }

        return $this->redirectToRoute('catalog_page');
    }

    private function existFreeBooks(BookCopyRepository $bookCopyRepository, BookCopy $bookCopy) {
        $bookCopyId = $bookCopy->getId();
        $countFreeBooks = $this->getCountFreeBooks($bookCopyRepository, $bookCopyId);
        return $countFreeBooks > 0;
    }

    /**
     * @param Request $request
     * @param BookCopy $bookCopy
     * @param BookCopyRepository $repository
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/{id}/rate-book", requirements={"\d+"}, name="rate_book")
     */
    public function rateBook(Request $request, BookCopy $bookCopy, BookCopyRepository $repository)
    {
        if ($request->isXmlHttpRequest()) {
            $score = $request->get('score');
            $rating = $this->setRating($bookCopy, $score);

            $this->saveEntity($rating);
            $result['rate'] = $score;

            return $this->json($result);
        }

        $rates = $repository->userRateBook($this->getUser()->getId(), $bookCopy->getId());
        return $this->redirectToRoute('catalog_show', [
            'id' => $bookCopy->getId(),
            'rates' => $rates,
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/{id}/favorite-book", requirements={"\d+"}, name="favorite_book")
     * @param Request $request
     * @param BookCopy $bookCopy
     * @param BookCopyRepository $repository
     * @return Response
     */
    public function addFavoritesBook(Request $request, BookCopy $bookCopy, BookCopyRepository $repository)
    {
        if ($request->isXmlHttpRequest()) {
            /** @var User $user */
            $user = $this->getUser();

            $json = $this->handleLikeAction($bookCopy, $user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $bookCopyId = $bookCopy->getId();
            $countLike = $repository->countLike($bookCopyId);
            $json['countLike'] = $countLike['0']['countLike'];
            return $this->json($json);
        }

        $bookCopyId = $bookCopy->getId();
        $json[] = $bookCopyId;

        return $this->json($json);
    }

    /**
     * @param BookCopy $bookCopy
     * @Route("/{id}/download-book", name="download_book_file")
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadFile(BookCopy $bookCopy)
    {
        try {
            $file = $bookCopy->getFilePath();
            if (!$file) {
                $array = [
                    'status' => 0,
                    'message' => 'File does not exist'
                ];

                return new JsonResponse($array, 200);
            }

            return $this->download($bookCopy, $file);

        } catch (Exception $exception) {
            $array = array(
                'status' => 0,
                'message' => 'Download error'
            );
            return new JsonResponse ($array, 400);
        }
    }

    /**
     * @Route("/comment/{id}/new", name="comment_new")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param BookCopy $bookCopy
     * @return Response
     */
    public function commentNew(Request $request, BookCopy $bookCopy): Response
    {
        $comment = $this->setNewComment($bookCopy);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveEntity($comment);
            return $this->redirectToRoute('catalog_show', ['id' => $bookCopy->getId()]);
        }

        return $this->render('catalog/comment_form_error.html.twig', [
            'post' => $bookCopy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param BookCopy $bookCopy
     * @return Response
     */
    public
    function commentForm(BookCopy $bookCopy): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('catalog/_comment-add.html.twig', [
            'post' => $bookCopy,
            'form' => $form->createView(),
        ]);
    }


    private
    function getRealEntities($entities)
    {
        $realEntities = [];
        foreach ($entities as $entity) {
            /**
             * @var BookCopy $entity
             */
            $realEntities[$entity->getId()] = [
                'name' => $entity->getBook()->getName(),
                'description' => $entity->getBook()->getDescription(),
                'imagePath' => $entity->getImagePath(),
                'bookCopyId' => $entity->getId()
            ];
        }

        return $realEntities;
    }

    /**
     * @param $whoHasBooks
     * @param $userId
     * @return int
     */
    private function findIssuanceId($whoHasBooks, $userId)
    {
        foreach ($whoHasBooks as $whoHasBook) {
            if ($whoHasBook['readerId'] === $userId) {
                return $whoHasBook['issuanceId'];
            }
        }

        return 0;
    }

    /**
     * @param BookCopyRepository $bookCopyRepository
     * @param $page
     * @param $limit
     * @param $options
     * @return mixed
     */
    private function getData(BookCopyRepository $bookCopyRepository, $page, $limit, $options)
    {
        $books = $bookCopyRepository->findForCatalog($page, $limit, $options);
        $countAll = $bookCopyRepository->countBookCopy();
        $result = [];
        if (!$books) {
            $result['entities']['error'] = "not found";
        } else {
            $result['entities']['error'] = "";
            $result['entities'] = $this->getRealEntities($books);
            $result['countAll'] = $countAll;
            $result['countItem'] = $limit;
        }
        return $result;
    }

    /**
     * @param $value
     * @param $rateAuthors
     * @param $column
     * @return false|int|string
     */
    private function isExistField($value, $rateAuthors, $column)
    {
        $result = $this->findEntity($value, $rateAuthors, $column);

        return is_numeric($result);
    }

    /**
     * @param $value
     * @param $data
     * @param $column
     * @return false|int|string
     */
    private function findEntity($value, $data, $column)
    {
        return array_search($value, array_column($data, $column));
    }

    /**
     * @param $entity
     */
    private function saveEntity($entity): void
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param BookCopy $bookCopy
     * @param $user
     * @return Issuance
     */
    private function setNewIssuance(BookCopy $bookCopy, $user): Issuance
    {
        $issuance = new Issuance();
        $issuance->setBookCopy($bookCopy);
        $issuance->setReader($user);
        return $issuance;
    }

    /**
     * @param BookCopyRepository $bookCopyRepository
     * @param $bookCopyId
     * @param $userId
     * @return false|int|string
     */
    private function getBookedRated(BookCopyRepository $bookCopyRepository, $bookCopyId, $userId)
    {
        $rateAuthors = $bookCopyRepository->findAuthorsOfBookRating($bookCopyId);

        return $this->isExistField($userId, $rateAuthors, 'authorId');
    }

    /**
     * @param BookCopyRepository $bookCopyRepository
     * @param $bookCopyId
     * @return string
     */
    private function getAverage(BookCopyRepository $bookCopyRepository, $bookCopyId): string
    {
        $averageRating = $bookCopyRepository->averageRating($bookCopyId);

        return $averageRating['0']['average'] ?? 'Пока нет отзывов';
    }

    /**
     * @return int
     */
    private function getUserId(): int
    {
        $user = $this->getUser();
        return isset($user) ? $user->getId() : 0;
    }

    /**
     * @param BookCopy $bookCopy
     * @param BookCopyRepository $bookCopyRepository
     * @param $userId
     * @return array
     */
    private function generateShowPageParameter(BookCopy $bookCopy, BookCopyRepository $bookCopyRepository, $userId): array
    {
        $bookCopyId = $bookCopy->getId();

        $existFreeBooks = $this->existFreeBooks($bookCopyRepository, $bookCopy);
        $average = $this->getAverage($bookCopyRepository, $bookCopyId);
        $whoHasBooks = $bookCopyRepository->whoHasBookCopy($bookCopyId);
        $isUserHasBooks = $this->isExistField($userId, $whoHasBooks, 'readerId');
        $issuanceId = $this->findIssuanceId($whoHasBooks, $userId);
        $bookRated = $this->getBookedRated($bookCopyRepository, $bookCopyId, $userId);
        $countLike = $bookCopyRepository->countLike($bookCopy->getId());

        return [
            'bookCopy' => $bookCopy,
            'existFreeBooks' => $existFreeBooks,
            'average' => $average,
            'whoHasBooks' => $whoHasBooks,
            'isUserHasBooks' => $isUserHasBooks,
            'issuanceId' => $issuanceId,
            'bookRated' => $bookRated,
            'countLike' => $countLike
        ];
    }

    /**
     * @param Request $request
     * @param BookRepository $repository
     * @param GenreRepository $genreRepository
     * @return array
     */
    private function generateParameterGenrePage(Request $request, BookRepository $repository, GenreRepository $genreRepository): array
    {
        $genreId = $request->get('genreId');

        $books = $repository->findByGenre($genreId);
        $genres = $genreRepository->findAllBooks();
        $genre = $genreRepository->find(1);

        $parameters = [
            'books' => $books,
            'genres' => $genres,
            'genre' => $genre
        ];
        return $parameters;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function parseFilterOptions(Request $request)
    {
        $options = [];
        $options['genreId'] = $request->get('genreId');
        $options['authorId'] = $request->get('authorId');
        $options['sortField'] = $request->get('sortField');
        $options['orderBy'] = $request->get('orderBy');
        return $options;
    }

    /**
     * @param BookCopyRepository $bookCopyRepository
     * @param GenreRepository $genreRepository
     * @param AuthorRepository $authorRepository
     * @param $page
     * @param $limit
     * @param $options
     * @return array
     */
    private function getCatalogPageParameters(BookCopyRepository $bookCopyRepository, GenreRepository $genreRepository,
                                              AuthorRepository $authorRepository, $page, $limit, $options): array
    {
        $genres = $genreRepository->findAll();
        $authorsOfBooks = $authorRepository->findAll();
        $booksCopy = $bookCopyRepository->findForCatalog($page, $limit, $options);

        return [
            'authorsOfBooks' => $authorsOfBooks,
            'genres' => $genres,
            'booksCopy' => $booksCopy
        ];
    }

    /**
     * @param BookCopy $bookCopy
     * @param User $user
     * @return mixed
     */
    private function handleLikeAction(BookCopy $bookCopy, User $user)
    {
        if ($user->bookCopyLiked($bookCopy)) {
            $json['action'] = 'unlike';
            $user->removeFavoritesBookCopy($bookCopy);
        } else {
            $json['action'] = 'like';
            $user->addFavoritesBookCopy($bookCopy);
        }
        return $json;
    }

    /**
     * @param BookCopy $bookCopy
     * @param $extension
     * @return string
     */
    private function generateDownloadFileName(BookCopy $bookCopy, $extension): string
    {
        return $bookCopy->getBook()->getName() . '.' . $extension;
    }

    /**
     * @param BookCopy $bookCopy
     * @param $file
     * @return BinaryFileResponse
     */
    private function download(BookCopy $bookCopy, $file): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', 'text/plain');
        $fileName = $this->generateDownloadFileName($bookCopy, 'pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);
        return $response;
    }

    /**
     * @param BookCopy $bookCopy
     * @return Comment
     */
    private function setNewComment(BookCopy $bookCopy): Comment
    {
        $comment = new Comment();

        $comment->setAuthor($this->getUser());
        $comment->setBookCopy($bookCopy);

        return $comment;
    }

    /**
     * @param BookCopy $bookCopy
     * @param $score
     * @return Rating
     */
    private function setRating(BookCopy $bookCopy, $score): Rating
    {
        $rating = new Rating();

        $rating->setBookCopy($bookCopy);
        $rating->setAuthor($this->getUser());
        $rating->setRating($score);

        return $rating;
    }

    /**
     * @param BookCopyRepository $bookCopyRepository
     * @param int $bookCopyId
     * @return int
     */
    private function getCountFreeBooks(BookCopyRepository $bookCopyRepository, int $bookCopyId): int
    {
        $countFreeBooks = $bookCopyRepository->countFreeBooks($bookCopyId);

        $item = $countFreeBooks['0'];
        if (!isset($item)) {
            return 0;
        }

        return isset($item['countFreeBooks']) ? $item['countFreeBooks'] + 1 : 0;
    }

    /**
     * @param BookCopyRepository $bookCopyRepository
     * @param $userId
     * @param $bookCopyId
     * @return false|int|string
     */
    private function userHasThisBook(BookCopyRepository $bookCopyRepository, $userId, $bookCopyId)
    {
        $whoHasBooks = $bookCopyRepository->whoHasBookCopy($bookCopyId);
        return $this->isExistField($userId, $whoHasBooks, 'readerId');
    }
}