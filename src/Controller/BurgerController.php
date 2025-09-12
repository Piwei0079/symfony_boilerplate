<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BurgerController extends AbstractController
{
    #[Route('/burgers', name: 'burgers_list')]
    public function list(): Response
    {
        $burgers = [['nom' => 'Buffalo Springfield', 'description' => 'une dinguerie', 'prix' => 10]];
 
        return $this->render('burgers/burgers_list.html.twig', [
            'burgers' => $burgers,
        ]);
    }
}

?>