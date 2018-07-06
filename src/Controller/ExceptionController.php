<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class HomeController
 * @package App\Controller
 */
class ExceptionController extends Controller
{
    /**
     * @Route("/exception")
     * @return Response
     */
    public function showException()
    {
//        throw $this->createNotFoundException('The product does not exist');
        return $this->render('error/other-error.html.twig');
    }
}