<?php

namespace App\Controller;

use App\Repository\BookCopyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="catalog")
     * @Route("/main", name="homepage")
     * @param BookCopyRepository $repository
     * @return Response
     */
    public function index(BookCopyRepository $repository)
    {
        $recentBooks = $repository->findLatest();
        $popularBooks = $repository->findPopular();

        return $this->render('home/index.html.twig', [
            'recentBooks' => $recentBooks,
            'popularBooks' => $popularBooks
        ]);
    }
}