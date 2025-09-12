<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    #[Route ('/', name: 'accueil')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }


    #[Route('/home', name: 'home')]
    public function oldIndex(): Response
    {
        return $this->render('base.html.twig');
    }
}

?>