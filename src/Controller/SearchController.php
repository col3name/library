<?php

namespace App\Controller;

use App\Repository\BookCopyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchController extends Controller
{
    /**
     * @Route("/search", name="ajax_search")
     * @param Request $request
     * @param BookCopyRepository $repository
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function search(Request $request, BookCopyRepository $repository)
    {
        if ($request->isXmlHttpRequest()) {
            $foundBooks = $this->handleSearch($request, $repository);

            return $this->json($foundBooks);
        }

        $foundBooks = $this->handleSearch($request, $repository);

        return $this->render('catalog/search.html.twig', [
            'foundBooks' => $foundBooks
        ]);
    }

    /**
     * @param Request $request
     * @param BookCopyRepository $repository
     * @return mixed
     */
    private function handleSearch(Request $request, BookCopyRepository $repository)
    {
        $search = $request->get('q') ?? '';
        $foundBooks = $repository->findBySearch($search);
        return $foundBooks;
    }
}