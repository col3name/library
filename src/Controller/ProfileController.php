<?php

namespace App\Controller;

use App\Entity\Issuance;
use App\Entity\User;
use App\Form\CustomFileType;
use App\Form\ProfileUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileController
 * @package App\Controller
 */
class ProfileController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/profile/{id}", requirements={"\d+"}, name="profile_page")
     */
    public function index(Request $request, User $user, UserRepository $userRepository)
    {
        if (!$this->isAuthorizedUser($user)) {
            return $this->redirectToRoute('login');
        }

        $userBooks = $userRepository->findIssuanceHistory($user->getId());
        $avatarEdit = $this->createForm(CustomFileType::class, $user);
        $avatarEdit->handleRequest($request);

        if ($avatarEdit->isSubmitted() && $avatarEdit->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('profile_page', [ 'id' => $user->getId() ]);
        }

        return $this->render('profile/index.html.twig', [
            'pageUser' => $user,
            'authorizedUser' => $this->getUser(),
            'userBooks' => $userBooks,
            'avatarEdit' => $avatarEdit->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param User $responseUser
     * @Route("/profile/{id}/delete-avatar", name="deleteAvatar")
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAvatar(Request $request, User $responseUser) {
        if (!$this->isAuthorizedUser($responseUser)) {
            return $this->redirectToRoute('login');
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('profile_page', ['
                id' =>$responseUser->getId()
            ]);
        }

        try {
            /**
             * @var User $user
             */
            $user->setAvatar(User::DEFAULT_AVATAR);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse(['result'=> $exception->getMessage()]);
        }

        return new JsonResponse(['success'=> true], 200);
    }

    /**
     * @Route("/profile/{id}/show-comment", requirements={"id" = "\d+"},  name="show_comment")
     * @param User $user
     * @return Response
     * @Security("has_role('ROLE_USER')")
     */
    public function showComments(User $user)
    {
        if (!$this->isAuthorizedUser($user)) {
            return $this->redirectToRoute('login');
        }

        return $this->render('profile/show-comment.html.twig', [
            'pageUser' => $user,
        ]);
    }

    /**
     * @param Request $request
     * @param UserRepository $repository
     * @return Response
     * @Security("has_role('ROLE_USER')")
     * @Route("/history-issuance", requirements={"\d+"}, name="history_issuance")
     */
    public function historyIssuance(Request $request, UserRepository $repository)
    {
        $user = $this->getUser();
        $page = $this->getPage($request);
        $issuanceHistory = $repository->findIssuanceHistory($user->getId(), $page, 10);

        if ($request->isXmlHttpRequest()) {
            $page = $this->getPage($request);

            $issuanceHistory = $repository->findIssuanceHistory($user->getId(), $page, 10);
            $result = $this->parseIssuance($issuanceHistory);

            $this->addFlash('success', 'Книга возращена');

            return $this->json($result);
        }

        $parameters = [
            'pageUser' => $user,
            'takenBooks' => $issuanceHistory,
            'page' => $page
        ];

        return $this->render('profile/history-issuance.html.twig', $parameters);
    }

    /**
     * @return Response
     * @Route("/liked-books", name="liked_books")
     * @Security("has_role('ROLE_USER')")
     */
    public function likedBooks()
    {
        $user = $this->getUser();

        $parameters = [
            'pageUser' => $user
        ];

        return $this->render('profile/liked.html.twig', $parameters);
    }

    /**
     * @return Response
     * @Route("/readed-book", name="readed_book")
     * @Security("has_role('ROLE_USER')")
     */
    public function readedBooks() {
        $user = $this->getUser();

        $parameters = [
            'pageUser' => $user
        ];

        return $this->render('profile/readed-books.html.twig', $parameters);
    }

    /**
     * @param Request $request
     * @param UserRepository $repository
     * @return Response
     * @Route("/my-books", requirements={"\d+"}, name="taken_book")
     * @Security("has_role('ROLE_USER')")
     */
    public function takenBook(Request $request, UserRepository $repository)
    {
        $user = $this->getUser();
        $page = $this->getPage($request);
        $readerId = $user->getId();
        $takenBooks = $repository->findTakenBook($readerId, $page, 200);

        if ($request->isXmlHttpRequest()) {
            $page = $this->getPage($request);

            $issuanceHistory = $repository->findTakenBook($readerId, $page, 210);
            $result = $this->parseIssuance($issuanceHistory);

            return $this->json($result);
        }

        $parameters = [
            'pageUser' => $user,
            'takenBooks' => $takenBooks,
            'page' => $page
        ];

        return $this->render('profile/show-taken-books.html.twig', $parameters);
    }

    /**
     * @Route("/{id}/settings", requirements={"\d+"}, name="edit-profile")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if (!$this->isAuthorizedUser($user)) {
            return $this->redirectToRoute('login');
        }

        $oldPassword = $user->getPassword();

        $form = $this->createForm(ProfileUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setPassword($user, $passwordEncoder, $oldPassword);
//            $this->getDoctrine()->getManager()->flush();

            $redirectParameters = [
                'id' => $user->getId()
            ];

//            return $this->redirectToRoute('profile_page', $redirectParameters);
        }

        $redirectParameters = [
            'user' => $user,
            'form' => $form->createView(),
        ];

        return $this->render('profile/edit.html.twig', $redirectParameters);
    }

    /**
     * @Route("/profile/return-book/{id}", requirements={"\d+"}, name="returnBook")
     * @param Issuance $issuance
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function returnBook(Issuance $issuance, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $result = $this->handleXmlHttpRequest($issuance);

            return $this->json($result);
        }

        $user = $this->getUser();
        $redirectParameters = ['id' => $user];

        return $this->redirectToRoute('profile_page', $redirectParameters);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function isAuthorizedUser(User $user)
    {
        return ($user === $this->getUser());
    }

    /**
     * @param $entities
     * @return array
     */
    private function getRealEntities($entities)
    {
        $realEntities = [];
        foreach ($entities as $entity) {
            /** @var Issuance $entity */
            $realEntities[$entity->getId()] = [
                'id' => $entity->getBookCopy()->getId(),
                'count' => $entity->getBookCopy()->getCount(),
                'imagePath' => $entity->getBookCopy()->getImagePath(),
                'name' => $entity->getBookCopy()->getBook()->getName(),
                'description' => $entity->getBookCopy()->getBook()->getDescription(),
            ];

        }

        return $realEntities;
    }

    /**
     * @param $issuanceHistory
     * @return mixed
     */
    private function parseIssuance($issuanceHistory)
    {
        $result = [];
        if (!$issuanceHistory) {
            $result['entities']['error'] = "not found";
        } else {
            $result['entities'] = [];
            $result['entities'] = $this->getRealEntities($issuanceHistory);
        }
        return $result;
    }

    /**
     * @param Request $request
     * @return int|mixed
     */
    private function getPage(Request $request)
    {
        return $request->get('page') ?? 1;
    }

    /**
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param String $oldPassword
     */
    private function setPassword(User $user, UserPasswordEncoderInterface $passwordEncoder, String $oldPassword): void
    {
        var_dump($user->getPlainPassword());

        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
    }

    /**
     * @param Issuance $issuance
     * @return array
     */
    private function handleXmlHttpRequest(Issuance $issuance): array
    {
        $now = new \DateTime();
        $issuance->setReleaseDate($now);
        $entityManager = $this->getDoctrine()->getManager();
        $result['error'] = false;

        try {
            $entityManager->flush();
        } catch (\Exception $ex) {
            $result['error'] = true;
        }
        return $result;
    }
}
