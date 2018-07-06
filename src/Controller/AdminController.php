<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Author;
use App\Entity\BookCopy;
use App\Form\GenreType;
use App\Form\BookCopyType;
use App\Form\AuthorBookType;
use App\Repository\AuthorRepository;
use App\Repository\BookCopyRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AdminController
 * @package App\Controller
 * @Security("has_role('ROLE_ADMIN')")
 * @Security("has_role('IS_AUTHENTICATED_FULLY')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_page")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/book", name="admin_show_book")
     * @Method("GET")
     * @param Request $request
     * @param BookCopyRepository $bookCopyRepository
     * @return Response
     */
    public function showBook(Request $request, BookCopyRepository $bookCopyRepository)
    {
        $page = $request->get('page') ?? 1;
//        $limit = $request->get('limit') ?? BookCopy::NUM_ITEMS;
//        $orderBy = $request->get('orderBy') ?? 'ASC';

        $books = $bookCopyRepository->findForCatalog($page);

        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
            'page' => $page
        ]);
    }

    /**
     * @param Request $request
     * @param Book $book
     * @param AuthorRepository $authorRepository
     * @return Response
     * @Security("has_role('IS_AUTHENTICATED_FULLY')")
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/add-book", name="addBook")a
     */
    public function addAuthorForBook(Request $request, Book $book, AuthorRepository $authorRepository)
    {
        $authorId = $request->get('authorId') ?? 1;
        /** @var Author $author */
        $author = $authorRepository->findById($authorId);

        echo "<pre>";
//        var_dump($author);
        echo "</pre>";

//        $author = new Author();
//        $author->setName('Вася');
//        $author->setBooks($book);

        $book->addAuthorsBook($author);

//        $entityManager = $this->getEntityManager();
//        $entityManager->persist($author);
//        $entityManager->persist($book);
//        $entityManager->flush();
        $authors = $authorRepository->findAll();

        return $this->render('admin/addAuthorbook.html.twig', [
            'authors' => $authors
        ]);
    }

    /**
     * @Route("/admin/book/edit/{id}", requirements={"id": "\d+"}, name="book_edit")
     * @Security("has_role('IS_AUTHENTICATED_FULLY')")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param BookCopy $bookCopy
     * @return Response
     */
    public function edit(Request $request, BookCopy $bookCopy): Response
    {
        $bookCopy->setImagePath(new File($bookCopy->getImagePath()));
        $form = $this->createForm(BookCopyType::class, $bookCopy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->persistEntity($bookCopy);

//            $entityManager =  $this->getDoctrine()->getManager();
//            $entityManager->flush();
            $message = 'Книга "' . $bookCopy->getBook()->getName() . '" обновлена';
            $this->addFlash('success', $message);

            return $this->redirectToRoute('admin_show_book');
        }

        return $this->render('admin/book/edit.html.twig', [
            'bookCopy' => $bookCopy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/book/newBook", requirements={"id": "\d+"}, name="book_new")
     * @Security("has_role('IS_AUTHENTICATED_FULLY')")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newBook(Request $request): Response
    {
        $bookCopy = new BookCopy();

        $form = $this->createForm(BookCopyType::class, $bookCopy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = 'Добавлена новая книга "' . $bookCopy->getBook()->getName() . '"';
            $this->addFlash('success', $message);
            $this->persistEntity($bookCopy);

            return $this->redirectToRoute('admin_show_book', [
                'id' => $bookCopy->getId()
            ]);
        }

        $parameters = [
            'bookCopy' => $bookCopy,
            'form' => $form->createView(),
        ];

        return $this->render('admin/book/new-book.html.twig', $parameters);
    }

    /**
     * @Route("/admin/book/book-delete/{id}", requirements={"id": "\d+"}, name="book_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param BookCopy $bookCopy
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, BookCopy $bookCopy)
    {
//        $submittedToken = $request->request->get('token');

        $bookName = $bookCopy->getBook()->getName();
        $this->removeEntity($bookCopy);

        $this->addFlash('success', 'Книга "' . $bookName . '" удалена');

        return $this->redirectToRoute('admin_show_book');
    }

    /**
     * @Route("/admin/book/new-book-author", name="new_book_author")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newBookAuthor(Request $request)
    {
        $options = [
            'entity' => new Author,
            'formType' => AuthorBookType::class,
            'successRedirectRoute' => 'admin_show_book',
            'failedRedirectRoute' => 'new_book_author',
            'view' => 'admin/book/new-book-author.html.twig'
        ];

        $this->addFlash('success', 'Автор добавлен');

        return $this->newEntity($request, $options);
    }

    /**
     * @Route("/admin/book/new-genre", name="new_genre")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newGenre(Request $request)
    {
        $options = [
            'entity' => new Genre,
            'formType' => GenreType::class,
            'successRedirectRoute' => 'admin_show_book',
            'failedRedirectRoute' => 'new_genre',
            'view' => 'admin/book/new-genre.html.twig'
        ];

        $this->addFlash('success', 'Жанр добавлен');

        return $this->newEntity($request, $options);
    }

    /**
     * @param Request $request
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    private function newEntity(Request $request, array $options)
    {
        $entity = $options['entity'];
        $formType = $options['formType'];
        $successRedirectRoute = $options['successRedirectRoute'];
        $successRedirectParameter = $options['successRedirectParameter'] ?? [];
        $failedRedirectRoute = $options['failedRedirectRoute'];
        $view = $options['view'];
        $renderParameters = $options['renderParameters'] ?? [];

        $form = $this->createForm($formType, $entity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->tryPersist($entity, $successRedirectRoute, $failedRedirectRoute, $successRedirectParameter);
        }

        $renderParameters['form'] = $form->createView();

        return $this->render($view, $renderParameters);
    }

    /**
     * @param $entity
     * @param $successRedirectRoute
     * @param $failedRedirectRoute
     * @param array $successRedirectParameter
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function tryPersist($entity, $successRedirectRoute, $failedRedirectRoute, array $successRedirectParameter = []): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        try {
            $entityManager->flush();
        } catch (\Exception $ex) {
            return $this->redirectToRoute($failedRedirectRoute);
        }

        return $this->redirectToRoute($successRedirectRoute, $successRedirectParameter);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function getEntityManager(): \Doctrine\Common\Persistence\ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param $countBookCopy
     * @return bool
     */
    private function isPossibleAddCopy($countBookCopy): bool
    {
        return $countBookCopy < BookCopy::MAX_COUNT;
    }

    /**
     * @param BookCopy $bookCopy
     * @param $countBookCopy
     */
    private function incrementBookCopyCount(BookCopy $bookCopy, $countBookCopy): void
    {
        $bookCopy->setCount($countBookCopy + 1);
        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }

    /**
     * @param $entity
     */
    private function persistEntity($entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * @param $entity
     * @return bool
     */
    private function removeEntity($entity): bool
    {
        $isSuccess = true;

        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        $entityManager->flush();
        return $isSuccess;
    }

    /**
     * @param $selectedId
     * @param $statues
     * @return false|int|string
     */
    private function isValidStatus($selectedId, $statues)
    {
        return array_search($selectedId, array_column($statues, 'id'));
    }
}