<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sauce;

final class SauceController extends AbstractController
{
 
    #[Route('/sauce/list', name: 'app_sauce_list')] 
    public function list(EntityManagerInterface $entityManager): Response 
    {

        $repository = $entityManager->getRepository(Sauce::class); 
        $sauces = $repository->findAll(); 
        dd($sauces);

        return new Response('Liste des sauces(voir le dump ci-dessus)');
    }


    #[Route('/sauce', name: 'app_sauce')]
    public function index(): Response
    {
        return $this->render('sauce/index.html.twig', [
            'controller_name' => 'SauceController',
        ]);
    }

    #[Route('/sauce/create/{name}', name: 'app_sauce_create')]
    public function create(EntityManagerInterface $entityManager, string $name): Response
    {
        $sauce = new Sauce();
        $sauce->setName($name); 
    

        $entityManager->persist($sauce);
        $entityManager->flush();

        $this->addFlash('success', 'La sauce "' . $name . '" a été créée avec succès.');

        return $this->redirectToRoute('app_sauce_list'); // Redirect to the list after creation
    }

    #[Route('/sauce/update/{id}/{name}', name: 'app_sauce_update')]
    public function update(EntityManagerInterface $entityManager, Sauce $sauce, string $name): Response
    {
        $sauce->setName($name); // Utilisez setNom()

        $entityManager->flush();

        $this->addFlash('success', 'La sauce "' . $sauce->getName() . '" a été modifiée avec succès.'); // Utilisez getNom()

        return $this->redirectToRoute('app_sauce_list'); // Redirect to the list after editing
    }

    #[Route('/sauce/delete/{id}', name: 'app_sauce_delete')]
    public function delete(EntityManagerInterface $entityManager, Sauce $sauce): Response
    {
        $nameSauce = $sauce->getName(); 
        $entityManager->remove($sauce);
        $entityManager->flush();

        $this->addFlash('success', 'La sauce "' . $nameSauce . '" a été supprimée avec succès.');

        return $this->redirectToRoute('app_sauce_list'); // Redirect to the list after deletion
    }

    
}