<?php

namespace App\Controller;

use App\Entity\Oignon; 
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Routing\Attribute\Route;

final class OignonController extends AbstractController
{
 
    #[Route('/oignon/list', name: 'app_oignon_list')] 
    public function list(EntityManagerInterface $entityManager): Response 
    {

        $repository = $entityManager->getRepository(Oignon::class); 
        $oignons = $repository->findAll(); 
        dd($oignons);

        return new Response('Liste des oignons (voir le dump ci-dessus)');
    }


    #[Route('/oignon', name: 'app_oignon')]
    public function index(): Response
    {
        return $this->render('oignon/index.html.twig', [
            'controller_name' => 'OignonController',
        ]);
    }

    #[Route('/oignon/create/{name}', name: 'app_oignon_create')]
    public function create(EntityManagerInterface $entityManager, string $name): Response
    {
        $oignon = new Oignon();
        $oignon->setName($name); 

        $entityManager->persist($oignon);
        $entityManager->flush();

        $this->addFlash('success', 'L\'oignon "' . $name . '" a été créé avec succès.');

        return $this->redirectToRoute('app_oignon_list'); // Redirect to the list after creation
    }

    #[Route('/oignon/update/{id}/{name}', name: 'app_oignon_update')]
    public function update(EntityManagerInterface $entityManager, Oignon $oignon, string $name): Response
    {
        $oignon->setName($name);


        $entityManager->flush();

        $this->addFlash('success', 'L\'oignon "' . $oignon->getName() . '" a été modifié avec succès.'); // Utilisez getNom()

        return $this->redirectToRoute('app_oignon_list'); // Redirect to the list after editing
    }

    #[Route('/oignon/delete/{id}', name: 'app_oignon_delete')]
    public function delete(EntityManagerInterface $entityManager, Oignon $oignon): Response
    {
        $nameOignon = $oignon->getName(); 
        $entityManager->remove($oignon);
        $entityManager->flush();

        $this->addFlash('success', 'L\'oignon "' . $nameOignon . '" a été supprimé avec succès.');

        return $this->redirectToRoute('app_oignon_list'); // Redirect to the list after deletion
    }
}
