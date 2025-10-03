<?php

namespace App\Controller;

use App\Entity\Pain; 
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class PainController extends AbstractController
{
 
    #[Route('/pain/list', name: 'app_pain_list')] 
    public function list(EntityManagerInterface $entityManager): Response 
    {

        $repository = $entityManager->getRepository(Pain::class); 
        $pains = $repository->findAll(); 
        dd($pains);

        return new Response('Liste des pains (voir le dump ci-dessus)');
    }


    #[Route('/pain', name: 'app_pain')]
    public function index(): Response
    {
        return $this->render('pain/index.html.twig', [
            'controller_name' => 'PainController',
        ]);
    }

    #[Route('/pain/create/{name}', name: 'app_pain_create')]
    public function create(EntityManagerInterface $entityManager, string $name): Response
    {
        $pain = new Pain();
        $pain->setName($name); 
     

        $entityManager->persist($pain);
        $entityManager->flush();

        $this->addFlash('success', 'Le pain "' . $name . '" a été créé avec succès.');

        return $this->redirectToRoute('app_pain_list'); // Redirect to the list after creation
    }

    #[Route('/pain/update/{id}/{name}', name: 'app_pain_update')]
    public function update(EntityManagerInterface $entityManager, Pain $pain, string $name): Response
    {
        $pain->setName($name); 
   

        $entityManager->flush();

        $this->addFlash('success', 'Le pain "' . $pain->getName() . '" a été modifié avec succès.'); // Utilisez getNom()

        return $this->redirectToRoute('app_pain_list'); // Redirect to the list after editing
    }

    #[Route('/pain/delete/{id}', name: 'app_pain_delete')]
    public function delete(EntityManagerInterface $entityManager, Pain $pain): Response
    {
        $namePain = $pain->getName();
        $entityManager->remove($pain);
        $entityManager->flush();

        $this->addFlash('success', 'Le pain "' . $namePain . '" a été supprimé avec succès.');

        return $this->redirectToRoute('app_pain_list'); // Redirect to the list after deletion
    }


}
