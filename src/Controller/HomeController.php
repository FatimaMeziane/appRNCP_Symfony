<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // je lui dit que cette fonction amene vers la  route home.html.twig
    #[Route('/', 'home.index')]
    public function index(): Response
    {
        return $this->render('pages/home.html.twig');
    }

   
}