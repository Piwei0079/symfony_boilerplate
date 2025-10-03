<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Burger;

final class BurgersController extends AbstractController
{
    #[Route('/burgers', name: 'app_burgers')]
    public function index(): Response
    {
        return $this->render('burgers/index.html.twig', [
            'controller_name' => 'BurgersController',
        ]);
    }
}
